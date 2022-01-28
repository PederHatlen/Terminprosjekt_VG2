from sqlite3 import connect
import MySQLdb

with MySQLdb.connect(
    "localhost",
    "root",
    "",
    "binærchatdb"
) as connection:
    print(connection)

    connection.cursor().execute("""INSERT INTO messages (conversation_id, sender_id, messagetext) VALUES (5, 1, 'Hello')""")
    connection.commit()
