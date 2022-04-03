import asyncio
from asyncio.windows_events import NULL
import time
import socket
import websockets
import json
import re
import MySQLdb
from termcolor import colored

conversations = {}

async def handler(websocket):
    userIP = colored("[{0}]".format(websocket.remote_address[0]), "magenta")
    try:
        global conversations
        print(userIP, colored("Connected", "green"))
        initData = json.loads(await websocket.recv())

        # Monstrosity of a sql query, but everything is needed
        cursor.execute("""SELECT 
            ws_tokens.conversation_id, 
            ws_tokens.user_id, 
            users.username,
            conv_users.color
        FROM ws_tokens 
        join users on users.user_id = ws_tokens.user_id 
        join conv_users 
            on conv_users.conversation_id = ws_tokens.conversation_id 
            and conv_users.user_id = ws_tokens.user_id 
        WHERE ws_tokens.token = %s and ws_tokens.active = 1 
        limit 1""", [str(initData["wsToken"])])
        conn.commit()  # Commiting this, else it would hold it and mess up next time

        ress = cursor.fetchall()
        if ress == (): raise Exception("Token is not valid.")
        
        try:
            chatId = ress[0][0]
            userId = ress[0][1]
            username = ("Torshken" if ress[0][2] == "1" else ress[0][2])
            color = ress[0][3]
        except:
            print(userIP, colored("Not enought values", "red"))
            await websocket.close()
            return False
        print(userIP, colored("Login-info received ", "green"))


        if chatId not in conversations.keys():
            conversations[chatId] = []
        conversations[chatId].append(websocket)
    except Exception as err:
        await websocket.close()
        print(userIP, colored("Something went wrong:", "red"), err)
        return False
    try:
        while websocket.open:
            try:
                message = json.loads(await websocket.recv())["msg"]
                message = re.sub(r"[^10 ]+", "", str(message[:255])).strip()
                # message = REpattern.sub(str(message[:255]), "")
                if len(message) != 0:
                    cursor.execute("""INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (%s, %s, %s)""", [str(chatId), str(userId), str(message)])
                    conn.commit()
                    unixtime = round(time.time() * 1000)
                    sendtTo = []
                    sendData = {
                        "color": color,
                        "time": unixtime,
                        "user": username,
                        "msg": message
                    }
                    for i in conversations[chatId]:
                        await i.send(json.dumps(sendData))
                        # await i.send("<p><span class=\"info\" style=\"color: "+ color + ";\"><span class='time'>["+ time.strftime("%H:%M:%S") + "]</span> " + ("Torshken" if str(clientDeets[2]) == "1" else clientDeets[2]) + ":</span> " + str(message) + "</p>")
                        sendtTo.append(colored(websocket.remote_address[0], "cyan"))
                    print(colored("Sendt to", "green"), colored(", ", "green").join(sendtTo))
            except websockets.exceptions.ConnectionClosed:
                break
            except Exception as err:
                print(userIP, colored("Something went wrong:", "red"), err)
                break
            await asyncio.sleep(1)
        conversations[chatId].remove(websocket)
        print(userIP, colored("Disconnected", "red"))
    except Exception as err:
        await websocket.close()
        print(userIP, colored("Something went wrong:", "red"), err)
        conversations[chatId].remove(websocket)
        return False
    

async def main():
    global conversations
    global conn
    global cursor

    try: conn = MySQLdb.connect("localhost", "root", "", "binærchatdb")
    except: 
        print(colored("Can't connect to database.", "red"))
        return False
    else: 
        print(colored("Connection to database was succesfull!", "green"))
    cursor = conn.cursor()

    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    s.connect(("8.8.8.8", 80))
    address = s.getsockname()[0]
    s.close()
    
    print(colored("Host ip:", "yellow"), colored(address, "cyan"))

    async with websockets.serve(handler, host = address, port = 5678):
        await asyncio.Future()  # run forever
    conn.close()

if __name__ == "__main__":
    asyncio.run(main())