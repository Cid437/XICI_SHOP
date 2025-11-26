
CREATE TABLE customer (
	customer_id intEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
	title char(4),
	lname varchar(200) NOT NULL,
	fname varchar(200),
	addressline mediumtext,
	town varchar(200),
	phone varchar(20),
	zipcode varchar(20)
);

CREATE TABLE orderinfo (
	orderinfo_id integer NOT NULL PRIMARY KEY AUTO_INCREMENT,
	date_placed date,
	date_shipped date,
	shipping numeric(7,2),
	status ENUM('processing', 'delivered', 'canceled', 'shipped'),
	customer_id integer NOT NULL,
	INDEX(customer_id),
	CONSTRAINT orderinfo_customer_id_fk FOREIGN KEY (customer_id) REFERENCES customer(customer_id) ON DELETE CASCADE
);

CREATE TABLE item(
	item_id integer NOT NULL PRIMARY KEY AUTO_INCREMENT,
	description varchar(255),
	cost_price numeric(7,2),
	sell_price numeric(7,2),
	img_path LONGTEXT
);

CREATE TABLE stock (
	item_id integer NOT NULL PRIMARY KEY,
	quantity integer,
	CONSTRAINT stock_item_id_fk FOREIGN KEY (item_id) REFERENCES item(item_id)
);

CREATE TABLE orderline (
	orderinfo_id integer NOT NULL, 
	item_id integer NOT NULL, 
	quantity integer NOT NULL, 
	PRIMARY KEY(orderinfo_id, item_id),
	INDEX(item_id),
	INDEX(orderinfo_id),

	CONSTRAINT orderline_item_id_fk FOREIGN KEY (item_id) REFERENCES item(item_id) ON DELETE CASCADE ,
	CONSTRAINT orderline_orderinfo_id_fk FOREIGN KEY (orderinfo_id) REFERENCES orderinfo(orderinfo_id) ON DELETE CASCADE
	);







