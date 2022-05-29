import asyncio
import random
import math
import time
import socket
import websockets
import json
import re
import MySQLdb
import os
from termcolor import colored

# Colored output Table
# IP/property	=	cyan
# Client	 	=	magenta
# Server		=	yellow
# Error/Discon	=	red
# Success		=	green
# Info			=	blue
# id			=	white

# Declaring global variables
__location__ = os.path.realpath(os.path.join(os.getcwd(), os.path.dirname(__file__)))
conversations = {}
externalSQL = False
arbeidsmiljolovenMode = True

# From https://stackoverflow.com/a/40559005 and https://stackoverflow.com/a/13110762
# Need .encode().decode() because of pythons extensive hatred of the norwegian characters
def decode_binary_string(s):
    return ''.join(chr(int(s[i*8:i*8+8],2)) for i in range(len(s)//8)).encode("windows-1252").decode("utf-8")

async def send_arbeidsmiljo_msg(userIP, chatId):
	jsondata = json.loads(open(os.path.join(__location__, "servermessage.json"), "r", encoding="utf_8").read())
	messages = json.loads(open(os.path.join(__location__, "arbeid_messages.json"), "r", encoding="utf_8").read())
	msgNumber = math.floor(random.random()*len(messages))
	jsondata["msg"] = messages[msgNumber]
	jsondata["time"] = round(time.time() * 1000)
	print(userIP, colored("Activated arbeidsmiljøloven mode! Initializing propaganda blast. Message number", "yellow"), colored(msgNumber, "cyan"), colored("was chosen.", "yellow"))
	for i in conversations[chatId]:
		await i.send(json.dumps(jsondata))

async def init(userIP, websocket):
	# Load init data
	initData = json.loads(await websocket.recv())
	print(userIP, colored("Login-info received ", "green"))

	# Monstrosity of a sql query, but everything is needed
	# Fetches ws-connect token and extra user data
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
	WHERE ws_tokens.token = %s and ws_tokens.expires_at > NOW() 
	limit 1""", [str(initData["wsToken"])])
	conn.commit()  # Committing this, else it would hold it and mess up next time
	ress = cursor.fetchone()
	if ress == None: raise Exception("Token is not valid.")

	# Delete old tokens
	cursor.execute("""DELETE FROM ws_tokens WHERE token = %s or expires_at < NOW();""", [str(initData["wsToken"])])
	conn.commit()
	print(userIP, colored(f"Deleted {cursor.rowcount} token" + ("s" if cursor.rowcount != 1 else ""), "blue"))

	# Assign user to the conversation and make conversation if not exists
	if ress[0] not in conversations.keys():
		conversations[ress[0]] = []
	conversations[ress[0]].append(websocket)

	return ress

async def handler(websocket):
	# UserIP for colored text
	userIP = colored("[{0}]".format(websocket.remote_address[0]), "magenta")
	print(userIP, colored("Connected", "green"))
	try:
		# Init function, returns query results
		ress = await init(userIP, websocket)
		# Setting local values
		chatId = ress[0]
		userId = ress[1]
		username = ("Torshken" if ress[2] == "1" else ress[2])
		color = ress[3]
	except Exception as err:
		print(userIP, colored("Init went wrong:", "red"), err)
		await websocket.close()
		return False
	while websocket.open:
		try:
			# striping everything not binary, and ignoring message if nothing
			message = re.sub(r"[^10 ]+", "", json.loads(await websocket.recv())["msg"]).strip()
			if len(message) == 0: continue

			# Storing message and conversation data to database
			cursor.execute("""INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (%s, %s, %s)""", [str(chatId), str(userId), str(message)])
			conn.commit()

			# Initializing send data
			unixtime = round(time.time() * 1000)
			sendData = {
				"color": color,
				"time": unixtime,
				"user": username,
				"msg": message
			}
			sendtTo = []

			# Sending the message/data to all connected websockets
			for i in conversations[chatId]:
				await i.send(json.dumps(sendData))
				sendtTo.append(colored(websocket.remote_address[0], "cyan"))
			print(colored("Sendt to", "green"), colored(", ", "green").join(sendtTo))
			
			# Arbeidsmiljøloven mode, sends a random law to a conversation when a user writes hms or arbeidsmiljøloven
			decoded_string = decode_binary_string(message).lower()
			if arbeidsmiljolovenMode and (decoded_string == "arbeidsmiljøloven" or decoded_string == "arbeidsmiljoloven" or decoded_string == "arbeidsmiljloven" or decoded_string == "hms"):
				# Function for retrieving and sending random message
				await send_arbeidsmiljo_msg(userIP, chatId)
			
		except websockets.exceptions.ConnectionClosed:
			break
		except Exception as err:
			print(userIP, colored("Something went wrong:", "red"), err)
			break

		# Timeout 1 second for anti spam, messages are stored in the meantime
		await asyncio.sleep(1)
	# If the connection is lost/exception remove connection from conversation
	conversations[chatId].remove(websocket)
	await websocket.close()
	print(userIP, colored("Disconnected", "red"))
	return False
	

async def main():
	# Defining global variables
	global conversations
	global conn
	global cursor

	# Try connecting to database, database is set by externalSQL
	try: 
		if externalSQL: conn = MySQLdb.connect("10.0.13.38", "binaerio", "Zfn4mu8%wtEtMPD8Q4jMR4tL6^nS^CDdU@G3E90b", "binaerchatdb")
		else: conn = MySQLdb.connect("localhost", "root", "", "binaerchatdb")
	except: 
		print(colored("Can't connect to database.", "red"))
		return False
	else: 
		print(colored("Connection to database was succesfull!", "green"))
	cursor = conn.cursor()

	# Getting computers outbound ip (connecting to google and retrieving socket name)
	# Retrieves a local address
	s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
	s.connect(("8.8.8.8", 80))
	address = s.getsockname()[0]
	s.close()
	
	print(colored("Host ip:", "yellow"), colored(address, "cyan"))

	# Serving the websocket
	async with websockets.serve(handler, host = address, port = 5678):
		await asyncio.Future()  # run forever
	conn.close()

# If the document is not imported (Best practice)
if __name__ == "__main__":
	asyncio.run(main())