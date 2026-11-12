Home Page 

The Home will provide a section where users can check their previous reservation 
from either of the three services.

This page is the first page the user will see. 
This is where the user gets a general feel of what the website is about.

- MyProfile Account
This is where the user's data will be stored to include:
* email
* Password
* Phone number

KMK Reservations provides three separate services:

- Restaurant Reservations
  Where the user can tap the "explore services" button 
  that directs them to the Restaurant Reservationss webpage

  Page Color
  Background:#883DDB
  Accent:#A855F7
  Button:#6D28D9
  Text:#FFFFFF


- Accomodaions
 Where the user can tap the "explore services" button 
 that directs them to the Hotel Room Reservation's webpage

 Page Color
  Background:#17BEBB
  Accent:#2DD4BF
  Button:#0EASA4
  Text:#FFFFFF

-Driver Services 
  Where the user can tap the "explore services" button 
  that directs them to the driver Service webpage

  Page Color
  Background:#2388F5
  Accent:#60ASFA
  Button:#1D4EDB
  Text:#FFFFFF

- My Bookings
  This is where the user can check their previous and current Reservations
  which includes the date the reservation was made and other information entered in by the user.

XAMPP MySQL setup
-----------------
1. Start Apache and MySQL from the XAMPP Control Panel.
2. In phpMyAdmin, import database.sql. It creates the kmk_reservations database
   and its reservations table.
3. The application connects to 127.0.0.1 as the default XAMPP root user with
   an empty password. To use different settings, define DB_HOST, DB_NAME,
   DB_USER, and DB_PASSWORD for Apache/PHP before starting Apache.
4. Open the site through Apache (for example, http://localhost/Practice/Main).

Reservations are stored in MySQL and scoped to the current PHP browser session.
The project does not yet include account registration or sign-in.
