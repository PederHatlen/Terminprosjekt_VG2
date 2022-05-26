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

__location__ = os.path.realpath(
    os.path.join(os.getcwd(), os.path.dirname(__file__)))

conversations = {}
eksternSQL = True
arbeidsmiljolovenMode = True

async def handler(websocket):
	userIP = colored("[{0}]".format(websocket.remote_address[0]), "magenta")
	try:
		global conversations
		print(userIP, colored("Connected", "green"))
		initData = json.loads(await websocket.recv())
		print(userIP, colored("Login-info received ", "green"))

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
		WHERE ws_tokens.token = %s and ws_tokens.expires_at > NOW() 
		limit 1""", [str(initData["wsToken"])])
		conn.commit()  # Committing this, else it would hold it and mess up next time

		ress = cursor.fetchone()
		if ress == None: raise Exception("Token is not valid.")

		cursor.execute("""DELETE FROM ws_tokens WHERE token = %s or expires_at < NOW();""", [str(initData["wsToken"])])
		conn.commit()
		print(userIP, colored(f"Deleted {cursor.rowcount} token" + ("s" if cursor.rowcount != 1 else ""), "blue"))
		
		try:
			chatId = ress[0]
			userId = ress[1]
			username = ("Torshken" if ress[2] == "1" else ress[2])
			color = ress[3]
		except:
			print(userIP, colored("Not enought values", "red"))
			await websocket.close()
			return False

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
						sendtTo.append(colored(websocket.remote_address[0], "cyan"))
					print(colored("Sendt to", "green"), colored(", ", "green").join(sendtTo))
				if arbeidsmiljolovenMode and (message == "011000010111001001100010011001010110100101100100011100110110110101101001011011000110101011000011101110000110110001101111011101100110010101101110"):
					jsondata = json.loads(open(os.path.join(__location__, "servermessage.json"), "r", encoding="utf_8").read())
					messages = json.loads(open(os.path.join(__location__, "arbeid_messages.json"), "r", encoding="utf_8").read())
					msgNumber = math.floor(random.random()*len(messages))
					jsondata["msg"] = messages[msgNumber]
					jsondata["time"] = unixtime
					print(userIP, colored("Activated arbeidsmiljøloven mode! Initializing propaganda blast. Message number", "yellow"), colored(msgNumber, "cyan"), colored("was chosen.", "yellow"))
					for i in conversations[chatId]:
						await i.send(json.dumps(jsondata))

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

	try: 
		if eksternSQL:
			conn = MySQLdb.connect("10.0.13.38", "binaerio", "Zfn4mu8%wtEtMPD8Q4jMR4tL6^nS^CDdU@G3E90b", "binaerchatdb")
		else:
			conn = MySQLdb.connect("localhost", "root", "", "binaerchatdb")
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