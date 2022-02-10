import asyncio
import datetime
import socket
import websockets
import re
import MySQLdb

conversations = {}

async def handler(websocket):
    global conversations
    print(websocket.remote_address[0], "Connected")
    clientDeets = await websocket.recv()
    clientDeets = clientDeets.split(", ")

    cursor.execute("""SELECT * FROM conversation_users WHERE conversation_id = %s and user_id = %s limit 1""", [str(clientDeets[0]), str(clientDeets[1])])
    ress = cursor.fetchone()
    if ress == None:
        websocket.close()
        conn.close()
        return
    
    cursor.execute("""SELECT * FROM tokens WHERE token = %s AND user_id = %s AND expires_at > CURRENT_TIMESTAMP order by expires_at DESC LIMIT 1""", [str(clientDeets[3]), str(clientDeets[2])])
    ress = cursor.fetchone()
    if ress == None:
        websocket.close()
        conn.close()
        return

    if clientDeets[0] not in conversations.keys():
        conversations[clientDeets[0]] = []
    conversations[clientDeets[0]].append(websocket)
    
    while websocket.open:
        try:
            message = await websocket.recv()
            message = re.sub(r"/[^10 ]+/g", "", str(message))
            if not message.isspace():
                cursor.execute("""INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (%s, %s, %s)""", [str(clientDeets[0]), str(clientDeets[1]), str(message)])
                conn.commit()
                time = datetime.datetime.now()
                for i in conversations[clientDeets[0]]:
                    await i.send("<p><p><span class='time'>[" + time.strftime("%H:%M:%S") + "]</span> " + ("Torshken" if str(clientDeets[2]) == "1" else clientDeets[2]) + ": " + str(message) + "</p>")
                    print("Sendt to {0}".format(websocket.remote_address[0]))
        except:
            break
        await asyncio.sleep(1)
    conn.close()
    conversations[clientDeets[0]].remove(websocket)
    print(websocket.remote_address[0], "Disconnected")
    

async def main():
    global conversations
    global conn
    global cursor

    try: conn = MySQLdb.connect("localhost", "root", "", "binærchatdb")
    except: 
        print("Can't connect to database.")
        return False
    else: 
        print("Connection was succesfull!")
    cursor = conn.cursor()

    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    s.connect(("8.8.8.8", 80))
    address = s.getsockname()[0]
    s.close()
    
    print("Host ip: " + address)

    async with websockets.serve(handler, host = address, port = 5678):
        await asyncio.Future()  # run forever

if __name__ == "__main__":
    asyncio.run(main())