CREATE DATABASE e_commerce;
CREATE TABLE product(
    ref_product SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name_product VARCHAR(50) NOT NULL,
    price DECIMAL(8,2) NOT NULL, 
    description_ VARCHAR(255),
    PRIMARY KEY (ref_product)
);

CREATE TABLE customer(
    id_customer SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    date_create DATETIME NOT NULL,
    PRiMARY KEY (id_customer)
);

CREATE TABLE orders(
    id_orders SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    date_purchase DATETIME NOT NULL,
    id_customer SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (id_orders),
    FOREIGN KEY (id_customer) REFERENCES customer(id_customer)
);


CREATE TABLE order_product(
    ref_product SMALLINT UNSIGNED NOT NULL,
    id_orders SMALLINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price_purchase DECIMAL (8.2) NOT NULL,
    PRiMARY KEY (ref_product, id_orders),
    FOREIGN KEY (ref_product) REFERENCES product(ref_product),
    FOREIGN KEY (id_orders) REFERENCES orders(id_orders)
);


INSERT INTO product (name_product, price, description_)
VALUES ("Ecran OLED 4K", 225.95, "Un écran de grande qualité pour un confort
visuel inégalé."),
    ("Souris de precision", 50.90, "Une souris précise et ergonomique pour les professionnels exigeants."),
    ("Clavier gamer", 65.25, "Un clavier haut de gamme et stylé.");

INSERT INTO customer (firstname, lastname, email, date_create)
VALUES ("samir", "Dermis", "samird@monmail.com", "2022-06-21 10:35:20"),
    ("Loanne", "Carfou", "carfou.loanne@roumail.fr", "2020-01-20 18:22:31");


INSERT INTO orders(date_purchase, id_customer)
VALUES ("2024-06-05", 1),
    ("2024-06-05", 2);

INSERT INTO order_product(ref_product, id_orders, quantity, price_purchase)
VALUES (2, 1, 1, 50.90),
    (3,1,1,65.25),
    (1, 2, 2, 225.95);