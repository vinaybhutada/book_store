# Student : Anmol Bhargava, 1001213922

create table Customers (
   username   varchar(10) primary key,
   password   varchar(32),
   address    varchar(100),
   phone	  varchar(20),
   email      varchar(45)
);

create table ShoppingBasket (
   basketId   varchar(13) primary key,
   username	  varchar(10) references Customers (username)
);

create table Book (
	ISBN numeric(10) primary key,
	title varchar(20),
	author varchar(20),
	year numeric(5),
	price numeric(10,2),
	publisher varchar(20)		
);

create table Warehouse (
	warehousecode numeric(5) primary key,
	name varchar(20),
	address varchar(50),
	phone numeric(10)
);

create table Author (
	ssn numeric(10) primary key,
	name varchar(20),
	address varchar(50),
	phone numeric(10)
);

create table WrittenBy (
	ssn numeric(10) references author (ssn),
	ISBN numeric(10) references book (ISBN)
);

create table Stocks (
	ISBN numeric(10) references book (ISBN),
	warehousecode numeric(5) references warehouse (warehousecode),
	number numeric(5)
);

create table Contains (
	ISBN numeric(10) references book (ISBN),
	basketId varchar(13) references ShoppingBasket (basketId),
	number numeric(10)
);

create table ShippingOrder (
	ISBN numeric(10) references book (ISBN),
	warehousecode numeric(5) references warehouse (warehousecode),
	username varchar(10) references Customers (username),
	number numeric(10)
);