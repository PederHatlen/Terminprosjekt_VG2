from ast import pattern
import asyncio
import datetime
import socket
import websockets
import re
import MySQLdb
from termcolor import colored

conversations = {}

async def handler(websocket):
    userIP = colored("[{0}]".format(websocket.remote_address[0]), "magenta")
    try:
        global conversations
        print(userIP, colored("Connected", "green"))
        clientDeets = await websocket.recv()
        clientDeets = clientDeets.split(", ")
        try:
            chatid = clientDeets[0]
            user_id = clientDeets[1]
            username = clientDeets[2]
            token = clientDeets[3]
        except:
            print(userIP, colored("Correct data wasn't received", "red"))
            print(clientDeets)
            await websocket.close()
            return False
        print(userIP, colored("Login-info received ", "green"))

        cursor.execute("""SELECT * FROM conversation_users WHERE conversation_id = %s and user_id = %s limit 1""", [str(chatid), str(user_id)])
        ress = cursor.fetchall()
        if ress == None:
            print(userIP, colored("User doesn't have access", "red"))
            await websocket.close()
            return False
        cursor.execute("""SELECT * FROM tokens WHERE token = %s AND user_id = %s AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC LIMIT 1""", [str(token), str(user_id)])
        ress = cursor.fetchone()
        if ress == None:
            print(userIP, colored("Token is expired or doesn't exist", "red"))
            await websocket.close()
            return False


        if clientDeets[0] not in conversations.keys():
            conversations[clientDeets[0]] = []
        conversations[clientDeets[0]].append(websocket)
        
        while websocket.open:
            try:
                message = await websocket.recv()
                message = re.sub(r"""/[^10 ]+""", "", str(message[:255]))
                # message = REpattern.sub(str(message[:255]), "")
                if not message.isspace():
                    cursor.execute("""INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (%s, %s, %s)""", [str(clientDeets[0]), str(clientDeets[1]), str(message)])
                    conn.commit()
                    time = datetime.datetime.now()
                    sendtTo = []
                    for i in conversations[clientDeets[0]]:
                        await i.send("<p><p><span class='time'>[" + time.strftime("%H:%M:%S") + "]</span> " + ("Torshken" if str(clientDeets[2]) == "1" else clientDeets[2]) + ": " + str(message) + "</p>")
                        sendtTo.append(colored(websocket.remote_address[0], "cyan"))
                    print(colored("Sendt to", "green"), colored(", ", "green").join(sendtTo))
            except websockets.exceptions.ConnectionClosed:
                break
            except Exception as err:
                print(userIP, colored("Something went wrong:", "red"), err)
                break
            await asyncio.sleep(1)
        conversations[clientDeets[0]].remove(websocket)
        print(userIP, colored("Disconnected", "red"))
    except Exception as err:
        await websocket.close()
        print(userIP, colored("Something went wrong:", "red"), err)
        conversations[clientDeets[0]].remove(websocket)
    

async def main():
    global conversations
    global conn
    global cursor
    global REpattern
    REpattern = re.compile("[^10 ]")

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