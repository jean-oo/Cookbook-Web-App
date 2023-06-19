drop table favorite;
drop table comment1;
drop table contain;
drop table rating;
drop table photos_includes;
drop table recipeMethod;
drop table recipe_has;
drop table search;
drop table manager_account;
drop table user_account;
drop table location1;
drop table location2;
drop table typeByRegion1;
drop table typeByRegion2;
drop table account;
drop table typeByMethod;
drop table ingredient;



CREATE TABLE typeByRegion2 (
                               LOCAL_DISH CHAR(50),
                               COUNTRY CHAR(20),
                               PRIMARY KEY (LOCAL_DISH));
grant select on typeByRegion2 to public;


CREATE TABLE typeByRegion1 (
                               ID int,
                               LOCAL_DISH CHAR(50),
                               PRIMARY KEY (ID),
                               FOREIGN KEY (LOCAL_DISH) REFERENCES typeByRegion2 (LOCAL_DISH));
grant select on typeByRegion1 to public;

CREATE TABLE account (
                         ACCOUNT_ID                    INTEGER,
                         USERNAME                     CHAR(20),
                         EMAIL                       CHAR(30),
                         ACCOUNT_PASSWORD             CHAR(30),
                         PRIMARY KEY (ACCOUNT_ID));
grant select on account to public;

CREATE TABLE location2 (
                           POSTAL_CODE                   CHAR(20),
                           CITY                          CHAR(20),
                           PROVINCE 			      	  varchar(20),
                           PRIMARY KEY (POSTAL_CODE));
grant select on location2 to public;

CREATE TABLE location1(
                          POSTAL_CODE                   CHAR(20),
                          HOUSE#                        INTEGER,
                          STREET                        CHAR(50),
                          PRIMARY KEY (POSTAL_CODE, HOUSE#, STREET),
                          FOREIGN KEY (POSTAL_CODE) REFERENCES location2(POSTAL_CODE));
grant select on location1 to public;

CREATE TABLE user_account(
                             USER_ACCOUNT_ID               INTEGER,
                             COOKING_LEVEL                 INTEGER,
                             POSTAL_CODE                   CHAR(20) NOT NULL,
                             HOUSE#                        INTEGER NOT NULL,
                             STREET                        CHAR(50) NOT NULL,
                             PRIMARY KEY (USER_ACCOUNT_ID),
                             FOREIGN KEY (POSTAL_CODE,HOUSE#,STREET) REFERENCES location1(POSTAL_CODE,HOUSE#,STREET),
                             FOREIGN KEY (USER_ACCOUNT_ID) REFERENCES account(ACCOUNT_ID));
grant select on user_account to public;

CREATE TABLE manager_account(
                                MANAGER_ACCOUNT_ID               INTEGER,
                                PRIMARY KEY (MANAGER_ACCOUNT_ID),
                                FOREIGN KEY (MANAGER_ACCOUNT_ID) REFERENCES account(ACCOUNT_ID));
grant select on manager_account to public;

CREATE TABLE typeByMethod (
                              ID INTEGER,
                              METHOD CHAR(20),
                              PRIMARY KEY (ID));
grant select on typeByMethod to public;

CREATE TABLE ingredient (
                            INGREDIENT_NAME        CHAR(50),
                            PRIMARY KEY (INGREDIENT_NAME));
grant select on ingredient to public;

CREATE TABLE search (
                        ACCOUNT_ID                    INTEGER,
                        SEARCH_COUNTER                INTEGER,
                        INGREDIENT_NAME               CHAR(50),
                        PRIMARY KEY (ACCOUNT_ID, INGREDIENT_NAME),
                        FOREIGN KEY (ACCOUNT_ID) REFERENCES user_account(USER_ACCOUNT_ID),
                        FOREIGN KEY (INGREDIENT_NAME) REFERENCES ingredient(INGREDIENT_NAME));
grant select on search to public;

CREATE TABLE recipe_has (
                            ID                          INTEGER PRIMARY KEY,
                            CREATETIME                  DATE,
                            USER_ACCOUNT_ID             INTEGER NOT NULL,
                            REGION_ID                   INTEGER NOT NULL,
                            RECIPE_NAME                 CHAR(50) NOT NULL,
                            DETAILS                     CHAR(500),
                            DIFFICULTY					SMALLINT,
                            FOREIGN KEY (USER_ACCOUNT_ID) REFERENCES user_account(USER_ACCOUNT_ID) ON DELETE CASCADE,
                            FOREIGN KEY (REGION_ID) REFERENCES typeByRegion1(ID) ON DELETE CASCADE);
grant select on recipe_has to public;

CREATE TABLE recipeMethod (
                              METHOD_ID INTEGER,
                              RECIPE_ID INTEGER,
                              PRIMARY KEY (METHOD_ID, RECIPE_ID),
                              FOREIGN KEY (METHOD_ID) REFERENCES typeByMethod(ID),
                              FOREIGN KEY (RECIPE_ID) REFERENCES recipe_has(ID) ON DELETE CASCADE);
grant select on recipeMethod to public;

CREATE TABLE photos_includes (
                                 PHOTO_ID                  INTEGER,
                                 RECIPE_ID                 INTEGER,
                                 PHOTO_URL                 CHAR(500),
                                 PRIMARY KEY (PHOTO_ID, RECIPE_ID),
                                 FOREIGN KEY (RECIPE_ID) REFERENCES recipe_has ON DELETE CASCADE);
grant select on photos_includes to public;

CREATE TABLE rating (
                        USER_ACCOUNT_ID            INTEGER,
                        RECIPE_ID                  INTEGER,
                        SCORE                      INTEGER,
                        PRIMARY KEY (USER_ACCOUNT_ID, RECIPE_ID),
                        FOREIGN KEY (USER_ACCOUNT_ID) REFERENCES user_account(USER_ACCOUNT_ID),
                        FOREIGN KEY (RECIPE_ID) REFERENCES recipe_has(ID) ON DELETE CASCADE);

grant select on rating to public;

CREATE TABLE contain (
                         INGREDIENT_NAME             CHAR(50),
                         RECIPE_ID                   INTEGER,
                         PRIMARY KEY (INGREDIENT_NAME, RECIPE_ID),
                         FOREIGN KEY (INGREDIENT_NAME) REFERENCES ingredient(INGREDIENT_NAME),
                         FOREIGN KEY (RECIPE_ID) REFERENCES recipe_has(ID) ON DELETE CASCADE);
grant select on contain to public;

CREATE TABLE comment1 (
                          ID                            INTEGER,
                          CREATE_TIME                   CHAR(30),
                          RECIPE_ID                     INTEGER,
                          USER_ACCOUNT_ID               INTEGER,
                          PRIMARY KEY (ID),
                          FOREIGN KEY (USER_ACCOUNT_ID) REFERENCES user_account(USER_ACCOUNT_ID),
                          FOREIGN KEY (RECIPE_ID) REFERENCES recipe_has(ID) ON DELETE CASCADE);
grant select on comment1 to public;

CREATE TABLE favorite(
                         USER_ACCOUNT_ID               INTEGER,
                         RECIPE_ID                     INTEGER,
                         PRIMARY KEY (USER_ACCOUNT_ID, RECIPE_ID),
                         FOREIGN KEY (USER_ACCOUNT_ID ) REFERENCES user_account(USER_ACCOUNT_ID),
                         FOREIGN KEY (RECIPE_ID) REFERENCES recipe_has(ID) ON DELETE CASCADE);

insert into typeByRegion2
values('Sichuan cuisine', 'China');
insert into typeByRegion2
values('Vancouver cuisine', 'Canada');
insert into typeByRegion2
values('Contonese cuisine', 'China');
insert into typeByRegion2
values('Quebec cuisine', 'Canada');
insert into typeByRegion2
values('Osaka cuisine', 'Japan');
insert into typeByRegion2
values('Shandong cuisine', 'China');
insert into typeByRegion2
values('Taiwan cuisine', 'China');
insert into typeByRegion2
values('New York cuisine', 'the USA');
insert into typeByRegion2
values('Cucina Siciliana cuisine', 'Italy');
insert into typeByRegion2
values('Kagawaken cuisine', 'Japan');

insert into typeByRegion1
values(1, 'Sichuan cuisine');
insert into typeByRegion1
values(2, 'Shandong cuisine');
insert into typeByRegion1
values(3, 'Taiwan cuisine');
insert into typeByRegion1
values(4, 'Vancouver cuisine');
insert into typeByRegion1
values(5, 'Kagawaken cuisine');
insert into typeByRegion1
values(6, 'Quebec cuisine');
insert into typeByRegion1
values(7, 'New York cuisine');
insert into typeByRegion1
values(8, 'Cucina Siciliana cuisine');
insert into typeByRegion1
values(9, 'Osaka cuisine');
insert into typeByRegion1
values(10, 'Contonese cuisine');

insert into account
values(1, 'Cindy Thorne', '1@gmail.com',1);
insert into account
values(2, 'Zander Villalobos', '2@gmail.com',2);
insert into account
values(3, 'Pooja Croft', '3@gmail.com',3);
insert into account
values(4, 'Alma Drew', '4@gmail.com',4);
insert into account
values(5, 'Loren Bowen', '5@gmail.com',5);
insert into account
values(6, 'Alessia Mayer', '6@gmail.com',6);
insert into account
values(7, 'Helena Cuevas', '7@gmail.com',7);
insert into account
values(8, 'Gregor Hope', '8@gmail.com',8);
insert into account
values(9, 'Alfie Dean', '9@gmail.com',9);
insert into account
values(10, 'Jarrad Hatfield', '10@gmail.com',10);

insert into location2
values('v7gd3n','Vancouver','BC');
insert into location2
values('d8336n','Edmonton', 'Alberta');
insert into location2
values('438','Winnipeg', 'Manitoba');
insert into location2
values('d7fh2b', 'Victoria', 'BC');
insert into location2
values('38i', 'Fredericton', 'NB');
insert into location2
values('d83m', 'St.Johns', 'Newfoundland');
insert into location2
values('d3g', 'Halifax', 'NS');
insert into location2
values('ddd', 'Toronto', 'Ontario');
insert into location2
values('d9j3','Charlottetown', 'PEI');
insert into location2
values('dk3','QuebecCity', 'Quebec');

insert into location1
values('v7gd3n', 3333, '6223 Bateman St.');
insert into location1
values('d8336n', 2222, '589 Darwin Ln.');
insert into location1
values('438', 1111, '67 Seventh Av.');
insert into location1
values('d7fh2b', 4444, '3 Balding Pl.');
insert into location1
values('38i', 5555, '1956 Arlington Pl.');
insert into location1
values('d83m', 6666, '301 Putnam');
insert into location1
values('d3g', 7777, '5420 Telegraph Av.');
insert into location1
values('ddd', 8888, '5420 College Av.');
insert into location1
values('d9j3', 9999, '5720 McAuley St.');
insert into location1
values('dk3', 0000, '44 Upland Hts.');

insert into typeByMethod
values(1,'braise');
insert into typeByMethod
values(2,'deep-fried');
insert into typeByMethod
values(3,'stir-fried');
insert into typeByMethod
values(4,'saute');
insert into typeByMethod
values(5,'boil');
insert into typeByMethod
values(6,'stew');
insert into typeByMethod
values(7,'roast');

insert into ingredient
values('bacon');
insert into ingredient
values('beef fat');
insert into ingredient
values('butter');
insert into ingredient
values('chicken fat');
insert into ingredient
values('cocoa butter');
insert into ingredient
values('coconut or coconut oil');
insert into ingredient
values('hydrogenated fats and oils');
insert into ingredient
values('lard');
insert into ingredient
values('palm or palm kernel oil');
insert into ingredient
values('powdered whole milk solids');


insert into user_account
values(2, 3, 'd8336n', 2222, '589 Darwin Ln.');
insert into user_account
values(3, 5, '438', 1111, '67 Seventh Av.');
insert into user_account
values(4, 3, 'd7fh2b', 4444, '3 Balding Pl.');
insert into user_account
values(5, 1, '38i', 5555, '1956 Arlington Pl.');
insert into user_account
values(6, 2, 'd83m', 6666, '301 Putnam');
insert into user_account
values(7, 2, 'd3g', 7777, '5420 Telegraph Av.');
insert into user_account
values(8, 4, 'ddd', 8888, '5420 College Av.');
insert into user_account
values(9, 3, 'd9j3', 9999, '5720 McAuley St.');
insert into user_account
values(10, 5, 'dk3', 0000, '44 Upland Hts.');

insert into manager_account
values(1);


insert into search
values(2, 1, 'beef fat');
insert into search
values(3, 2, 'butter');
insert into search
values(4, 3, 'chicken fat');
insert into search
values(10, 1, 'cocoa butter');
insert into search
values(5, 6, 'coconut or coconut oil');
insert into search
values(6,1, 'hydrogenated fats and oils');
insert into search
values(7, 9, 'lard');
insert into search
values(8, 4, 'palm or palm kernel oil');
insert into search
values(9, 3, 'powdered whole milk solids');

insert into recipe_has
values (1, TO_DATE('2020-06-16', 'YYYY-MM-DD HH24:MI:SS'), 4, 3, 'taiwanese-style three cup chicken ', 'r2: first step...',1);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
insert into recipe_has
values(2,To_Date('2020-12-30', 'YYYY-MM-DD HH24:MI:SS'),2,2,'Sweet and Sour Pork','r2: first step...',2);
insert into recipe_has
values(3,To_Date('2020-06-17', 'YYYY-MM-DD HH24:MI:SS'),3,1,'Kung Pao Chicken','r3: first step...',3);
insert into recipe_has
values(4,To_Date('2020-06-18', 'YYYY-MM-DD HH24:MI:SS'),4,4,'Cal Smart Steak','r4: first step...',4);
insert into recipe_has
values(5,To_Date('2020-06-19', 'YYYY-MM-DD HH24:MI:SS'),5,5,'Beef Udon','r5: first step...',5);
insert into recipe_has
values(6,To_Date('2020-06-20', 'YYYY-MM-DD HH24:MI:SS'),6,6,'Poutine','r6: first step...',4);
insert into recipe_has
values(7,To_Date('2020-06-21', 'YYYY-MM-DD HH24:MI:SS'),7,7,'Slow Cooker Pot Roast','r7: first step...',3);
insert into recipe_has
values(8,To_Date('2020-06-22', 'YYYY-MM-DD HH24:MI:SS'),8,8,'Pasta alla Norma','r8: first step...',2);
insert into recipe_has
values(9,To_Date('2020-06-23', 'YYYY-MM-DD HH24:MI:SS'),9,9,'Okonomiyaki','r9: first step...',1);
insert into recipe_has
values(10,To_Date('2020-06-23', 'YYYY-MM-DD HH24:MI:SS'),10,10,'Barbecued Pork','r10: first step...',2);
insert into recipe_has
values(11, To_Date('2020-06-16', 'YYYY-MM-DD HH24:MI:SS'),10,3,'Taiwanese-Style Three Cup Chicken','r11: first step...',2);

insert into recipeMethod
values(1,1);
insert into recipeMethod
values(2,2);
insert into recipeMethod
values(3,3);
insert into recipeMethod
values(4,4);
insert into recipeMethod
values(5,5);
insert into recipeMethod
values(2,6);
insert into recipeMethod
values(5,6);
insert into recipeMethod
values(6,7);
insert into recipeMethod
values(5,8);
insert into recipeMethod
values(3,9);
insert into recipeMethod
values(4,9);
insert into recipeMethod
values(7,10);

insert into photos_includes
values(1,1,'/~jeanoo/1.png');
insert into photos_includes
values(2,2,'/~jeanoo/2.png');
insert into photos_includes
values(3,3,'/~jeanoo/3.png');
insert into photos_includes
values(4,4,'/~jeanoo/4.png');
insert into photos_includes
values(5,5,'/~jeanoo/5.png');
insert into photos_includes
values(6,6,'/~jeanoo/6.png');
insert into photos_includes
values(7,7,'/~jeanoo/7.png');
insert into photos_includes
values(8,8,'/~jeanoo/8.png');
insert into photos_includes
values(9,9,'/~jeanoo/9.png');
insert into photos_includes
values(10,10,'/~jeanoo/10.png');
insert into photos_includes
values(11,11,'/~jeanoo/1.png');

insert into rating
values(7,2,3);
insert into rating
values(7,3,4);
insert into rating
values(2,4,3);
insert into rating
values(7,5,5);
insert into rating
values(7,8,3);
insert into rating
values(10,2,3);
insert into rating
values(4,1,1);
insert into rating
values(5,3,5);
insert into rating
values(2,9,1);
insert into rating
values(8,7,2);

insert into contain
values('bacon', 6);
insert into contain
values('beef fat', 4);
insert into contain
values('butter', 7);
insert into contain
values('chicken fat', 1);
insert into contain
values('cocoa butter', 2);
insert into contain
values('coconut or coconut oil', 2);
insert into contain
values('hydrogenated fats and oils', 8);
insert into contain
values('lard', 3);
insert into contain
values('lard', 2);
insert into contain
values('lard', 10);
insert into contain
values('palm or palm kernel oil', 5);
insert into contain
values('powdered whole milk solids', 9);


insert into comment1
values(1, TO_DATE('2007-12-20', 'YYYY-MM-DD HH24:MI:SS'),11,2);
insert into comment1
values(2, TO_DATE('2020-07-16', 'YYYY-MM-DD HH24:MI:SS'),2,3);
insert into comment1
values(3, TO_DATE('2020-12-30', 'YYYY-MM-DD HH24:MI:SS'),1,4);
insert into comment1
values(4, TO_DATE('2020-07-17', 'YYYY-MM-DD HH24:MI:SS'),3,2);
insert into comment1
values(5, TO_DATE('2020-07-28', 'YYYY-MM-DD HH24:MI:SS'),4,6);
insert into comment1
values(6,TO_DATE('2020-07-29', 'YYYY-MM-DD HH24:MI:SS'),5,5);
insert into comment1
values(7, TO_DATE('2020-07-23', 'YYYY-MM-DD HH24:MI:SS'),6,9);
insert into comment1
values(8, TO_DATE('2020-07-25', 'YYYY-MM-DD HH24:MI:SS'),7,8);
insert into comment1
values(9, TO_DATE('2020-07-05', 'YYYY-MM-DD HH24:MI:SS'),3,10);
insert into comment1
values(10, TO_DATE('2020-07-01', 'YYYY-MM-DD HH24:MI:SS'),1,5);

insert into favorite
values(4,1);
insert into favorite
values(9,2);
insert into favorite
values(10,3);
insert into favorite
values(2,1);
insert into favorite
values(3,1);
insert into favorite
values(4,2);
insert into favorite
values(5,4);
insert into favorite
values(7,4);
insert into favorite
values(4,8);
insert into favorite
values(5,7);

commit work;
