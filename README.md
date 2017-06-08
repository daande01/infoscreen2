https://github.com/MartinThoma/ics-parser
- [ ] Multiple calendar URLs from itslearnig
- [ ] Only display necessary CSS  
- [ ] Load js using script  
- [ ] Store menu locally  
- [ ] Don't request .ical every time  
- [ ] GitHub api?  
- [ ] Clock  
- [ ] Infoga l�nk till GitHub-repo i README-filen usder rubriken Den h�r filen  
- [ ] Rename files  
- [ ] Describe how calendar CSS and script is loaded  

# Introduktion
# Konfigurera
## �ndra kalenderadress
Kalenderaddressen som anv�nds �r bel�gen i *eventcalendar.php* i form
av en klasskonstant med namnet *CALENDAR_URL*. F�r att byta till ett
annat kontos kalender byts v�rdet till dennes
kalenderaddress. Kalenderadressen kan hittas p� itslearnig under
*kalender/knappen med de tre punkterna/prenumerera. Byt ut "webcal://"
mot "http://".

## L�gga till och ta bort klasser
Alla klassnamn och dess f�rger finns lagrade i tabellen *classes* i
databasen *infoscreen*. Dessa kan �ndras med SQL. Exempelvis kan en
klass med namnet 'TE4a' och f�rgen magenta enligt f�ljande.  

```sql
INSERT INTO `classes`(`name`, `color`) VALUES ("TE4a", "ff00ff")
```

# Projektstuktur
Projektet best�r att ett flertal komponenter som samverkar.  

- Den del av sidan som tar hand om http-fr�gor befinner sig i index.php
- Kalendern hanteras av eventcalendar.php  
Tillh�rande CSS och skript finns i de respektive mapparna.
- Informationssidorna f�r l�rare och elever finns i teacherinfo.php
respektive studentinfo.php  

En utf�rligare beskrivning av respektive komponent finns nedan.  

# Kalender
## H�mta data fr�n itslearning
I itslearning finns det personliga kalendrar som �r tillg�ngliga f�r
alla som har tillg�ng till r�tt URL. Kalenderkomponenten anv�nder
detta f�r att h�mta informationen fr�n itslearning. Datan �r lagrad i
*ical*-format vilket �r ett standardformat f�r kalendrar. Kalendern
lagras tempor�rt i en fil som heter *its-calendar.ics*.

## Tolka .ics filen
F�r att tolka inneh�llet i *.ical*-filer anv�nds en modul av
MartinThoma. Modulen finns i en fil med namnet
*class.iCalReader.php*. Den finns tillg�nglig p�
GitHub:
[MartinToma's ics-parser](https://github.com/MartinThoma/ics-parser). Med
hj�lp av modulen extraheras start- och sluttid f�r h�ndelser samt
texten de inneh�ller och vilken kurs de tillh�r.

### Tid
Under tolkningsprocessen konverteras tid/datum-formatet i
*.ics*-filen<sup>[1](#ical-time)</sup> till ett format som kan tolkas
av MySQL<sup>[2](#MySQL-time)</sup>. Omvandlingen sker i funktionen
*icalTimeToMySQLTime($datetime)*. Funktionen �vers�tter �ven datum och
tid till r�tt tidszon.

## Mjukvarugr�nssnitt
Kalendern �r en klass men namnet *EventCalendar*. Den inneh�ller ett
antal publika funktioner:  

| Funktionsnamn                                            |
|----------------------------------------------------------|
| construct()                                              |
| clearEventsFromDB()                                      |
| refreshDB()                                              |
| getEvents([$weekCount = 1 [, $startWeek [, $classes]]])  |
| getEventCalendar($weekCount [, $startWeek [, $classes]]) |

### construct
Klassens konstruktor initierar en databasanslutning samt s�tter
tidszonen.

### clearEventsFromDB
Funktionen rensar ut alla h�ndelser ur databasen. Funktionen
*refreshDB* anropar denna automatiskt.  
Databasen beh�ver t�mmas innan en ny kalender h�mtas fr�n itslearning
f�r att undvika konflikter och f�r att h�ndelser som tagits bort p�
itslearing �ven skall f�rsvinna ur databasen.

### refreshDB
Funktionen �r det gr�nssnitt som anropas b�de internt och utifr�n f�r
att uppdatera databasens inneh�ll. Funktionen anropar sekvensiellt
funktioner f�r att h�mta data fr�n itslearning, tolka datan, infoga de
nya h�ndelserna samt utl�sa vilka klasser som deltar i de olika
h�ndelserna och infoga detta i databasen.  

Om en anslutningen till itslearning misslyckas returnerar funktionen
*false*, *true* annars.

### getEvents
F�r att externt h�mta information ur databasen rekommenderas denna
metod. Den returnerar en array, indexerad efter databasens
f�ltnamn. Som argument tar funktionen antalet veckor som skall
inkluderas, vilken vecka den skall b�rja p� samt ett filter f�r vilka
klasser som skall anv�ndas. Klasserna kan anges antingen som en array
eller om endast en klass s�kes; som en textstr�ng. Argumenten m�ste
inte anges och d� antas f�ljande effektiva standardv�rden:  

| Parameternamn | Standardv�rde    |
|---------------|-----------------:|
| weekCount     | 1                |
| startWeek     | nuvarande vecka  |
| classes       | alla             |

Notera att det i funktionshuvudet ter sig som att standardv�rderna
inte matchar de som angivits i ovanst�ende tabell. Detta beror p� att
standardv�rdena antas i funktionskroppen d� funktionsanrop inte �r
till�tet i argumentdeklarationen <sup>[3](#PHP-args)</sup>.

### getEventCalendar
Det grafiska gr�nssnittet h�mtas med denna funktion. Den returnerar en
textstr�ng inneh�llande HTML-koden f�r kalendern. Argumenten motsvarar
de till *getEvents*-metoden med undantaget att *$weekCount* inte kan
utel�mnas.

## Grafiskt gr�nssitt till kalendern
Det grafiska gr�nssnittet �r byggt i HTML, CSS samt en liten m�ngd
JavaScript. All CSS (borts�tt fr�n en ikon) �r byggd utan ramverk
(detta g�ller endast f�r kalendern, inte resterande sida).

Kalendern �r en HTML-tabell. Varja dag �r en <td>-tagg. Innuti denna
ligger h�ndelserna som 

# index.php

## .htaccess
Alla HTTP-fr�gor (med vissa undantag) dirigeras till index.php p�
grund av .htacces-filen. Om detta inte fungerar kan servern beh�va
omkonfigureras. En Apache-modul kan beh�vas anges som aktiv.

## Materialize
M�nga HTML/CSS/JavaScript-komponenter p� sidan skapas med hj�lp av ett
ramverk som heter *Materialize* och �r baserat p� Googles Material
Design <sup>[4](#material-design)</sup>. Ramverket �r tillg�ngligt
p� [materializecss.com](http://materializecss.com/).

# Meny
Navigationsmenyn best�r av b�de statiskt och dynamiskt inneh�ll. De
statiska elementen �r f�r att navigera till informationssidorna samt
start. De olika klassernas kalendrar genereras dynamiskt beroende p�
inneh�llet i tabellen *classes* i databasen. Materialize anv�nds f�r
layout samt det skript som tar fram och d�ljer menyn. Knappen som tar
fram menyn fungerar med hj�lp av ett skript som finns i *js/script.js*.

# L�rarinformation
L�rarinformationen �r skriven som en enkel HTML-fil med tillh�rande
CSS som anv�nder sig av Materialize. F�r att redigera informationen
�ndras texten i funktionen *getTeacherInfoContent* i filen *teacherinfo.php*.

# Klocka
Klockan som visas i �vre h�gra h�rnet drivs av JavaScript. Skriptet
utnyttjar en
[Date](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date)-instans
f�r att h�mta tiden fr�n klientens dator. En *formatTime*-funktion
anv�nds f�r att l�gga till nollor s� att tidsformatet HH:MM:SS
f�s. *setTimeOut* anv�nds f�r att uppdatera klockan. Den grafiska
visningen sker genom att �ndra inre HTML f�r ett element med id "clock".

# Den h�r filen
*README*-filer har som uppgift att ge anv�ndare viktig
information. Ofta har de (som i det h�r fallet) fil�ndelsen *.md*. Det
inneb�r att de �r skrivna i typs�ttningsspr�ket *Markdown*. Den h�r
filen �r skriven i
[GitHub Flavored Markdown](https://help.github.com/articles/about-writing-and-formatting-on-github/).  

Om filen �ppnas i en vanlig textredigerare kommer rubriker f�reg�s med
#, *kursiv text kommer vara omgiven av asterixer* osv. Detta beroro pa
att Markdown-filer �r t�nkta att konverteras till andra format. Detta
sker automatiskt p� sidor som GitHub. Den h�r filen b�r l�sas p�
projektets [GitHub-sida](https://www.google.se) (repository).  

# Utvecklare
Projektet utvecklades av Lukas Skystedt i kursen
Webbserverprogrammering 1 �r 2017.  

# F�rslag p� fortsatt arbete
- Anpassa sidan f�r mobila enheter.  
- Integrera schemat fr�n novasoftware
- 


---
<a name="ical-time">[1]</a> [Kanzaki, ical DateTime](http://www.kanzaki.com/docs/ical/dateTime.html)  
<a name="MySQL-time">[2]</a> [MySQL.com, Date and Time formats](https://dev.mysql.com/doc/refman/5.7/en/date-and-time-types.html)  
<a name="PHP-args">[3]</a> [PHP Manual, Default Arguments](http://php.net/manual/en/functions.arguments.php)  
<a name="material-design">[4]</a> [Google, Material Design](https://material.io/guidelines/)  
