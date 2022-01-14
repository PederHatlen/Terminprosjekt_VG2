package main

import (
	"database/sql"
	"fmt"
	"strconv"
	"time"

	_ "github.com/go-sql-driver/mysql"
	_ "github.com/gorilla/websocket"
)

type Users struct {
	ID         int    `json:"user_id"`
	Username   string `json:"username"`
	Password   string `json:"password"`
	Created_at string `json:"created_at"`
}

func sql_test() {

	fmt.Println(time.Now())

	db, err := sql.Open("mysql", "root:@/binærchatdb")
	if err != nil {
		panic(err)
	}
	defer db.Close()

	// Execute the query
	results, err := db.Query("SELECT * FROM users")
	if err != nil {
		panic(err.Error()) // proper error handling instead of panic in your app
	}

	for results.Next() {
		var user Users
		// for each row, scan the result into our tag composite object
		err = results.Scan(&user.ID, &user.Username, &user.Password, &user.Created_at)
		if err != nil {
			panic(err.Error()) // proper error handling instead of panic in your app
		}
		// and then print out the tag's Name attribute
		fmt.Printf(strconv.FormatInt(int64(user.ID), 10) + " " + user.Username + " " + user.Password + " " + user.Created_at + "\n")
	}
}
