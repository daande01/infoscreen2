https://github.com/MartinThoma/ics-parser
- [ ] Multiple calendar URLs from itslearnig
- [ ] Only display necessary CSS  
- [ ] Load js using script  
- [ ] Store menu locally  
- [ ] Don't request .ical every time  
- [ ] GitHub api?  
- [ ] Clock  
- [ ] Infoga länk till GitHub-repo i README-filen usder rubriken Den här filen  
- [ ] Rename files  
- [ ] Describe how calendar CSS and script is loaded  

# Introduktion
# Konfigurera
## Ändra kalenderadress
Kalenderaddressen som används är belägen i *eventcalendar.php* i form
av en klasskonstant med namnet *CALENDAR_URL*. För att byta till ett
annat kontos kalender byts värdet till dennes
kalenderaddress. Kalenderadressen kan hittas på itslearnig under
*kalender/knappen med de tre punkterna/prenumerera. Byt ut "webcal://"
mot "http://".

## Lägga till och ta bort klasser
Alla klassnamn och dess färger finns lagrade i tabellen *classes* i
databasen *infoscreen*. Dessa kan ändras med SQL. Exempelvis kan en
klass med namnet 'TE4a' och färgen magenta enligt följande.  

```sql
INSERT INTO `classes`(`name`, `color`) VALUES ("TE4a", "ff00ff")
```

# Projektstuktur
Projektet består att ett flertal komponenter som samverkar.  

- Den del av sidan som tar hand om http-frågor befinner sig i index.php
- Kalendern hanteras av eventcalendar.php  
Tillhörande CSS och skript finns i de respektive mapparna.
- Informationssidorna för lärare och elever finns i teacherinfo.php
respektive studentinfo.php  

En utförligare beskrivning av respektive komponent finns nedan.  

# Kalender
## Hämta data från itslearning
I itslearning finns det personliga kalendrar som är tillgängliga för
alla som har tillgång till rätt URL. Kalenderkomponenten använder
detta för att hämta informationen från itslearning. Datan är lagrad i
*ical*-format vilket är ett standardformat för kalendrar. Kalendern
lagras temporärt i en fil som heter *its-calendar.ics*.

## Tolka .ics filen
För att tolka innehållet i *.ical*-filer används en modul av
MartinThoma. Modulen finns i en fil med namnet
*class.iCalReader.php*. Den finns tillgänglig på
GitHub:
[MartinToma's ics-parser](https://github.com/MartinThoma/ics-parser). Med
hjälp av modulen extraheras start- och sluttid för händelser samt
texten de innehåller och vilken kurs de tillhör.

### Tid
Under tolkningsprocessen konverteras tid/datum-formatet i
*.ics*-filen<sup>[1](#ical-time)</sup> till ett format som kan tolkas
av MySQL<sup>[2](#MySQL-time)</sup>. Omvandlingen sker i funktionen
*icalTimeToMySQLTime($datetime)*. Funktionen översätter även datum och
tid till rätt tidszon.

## Mjukvarugränssnitt
Kalendern är en klass men namnet *EventCalendar*. Den innehåller ett
antal publika funktioner:  

| Funktionsnamn                                            |
|----------------------------------------------------------|
| construct()                                              |
| clearEventsFromDB()                                      |
| refreshDB()                                              |
| getEvents([$weekCount = 1 [, $startWeek [, $classes]]])  |
| getEventCalendar($weekCount [, $startWeek [, $classes]]) |

### construct
Klassens konstruktor initierar en databasanslutning samt sätter
tidszonen.

### clearEventsFromDB
Funktionen rensar ut alla händelser ur databasen. Funktionen
*refreshDB* anropar denna automatiskt.  
Databasen behöver tömmas innan en ny kalender hämtas från itslearning
för att undvika konflikter och för att händelser som tagits bort på
itslearing även skall försvinna ur databasen.

### refreshDB
Funktionen är det gränssnitt som anropas både internt och utifrån för
att uppdatera databasens innehåll. Funktionen anropar sekvensiellt
funktioner för att hämta data från itslearning, tolka datan, infoga de
nya händelserna samt utläsa vilka klasser som deltar i de olika
händelserna och infoga detta i databasen.  

Om en anslutningen till itslearning misslyckas returnerar funktionen
*false*, *true* annars.

### getEvents
För att externt hämta information ur databasen rekommenderas denna
metod. Den returnerar en array, indexerad efter databasens
fältnamn. Som argument tar funktionen antalet veckor som skall
inkluderas, vilken vecka den skall börja på samt ett filter för vilka
klasser som skall användas. Klasserna kan anges antingen som en array
eller om endast en klass sökes; som en textsträng. Argumenten måste
inte anges och då antas följande effektiva standardvärden:  

| Parameternamn | Standardvärde    |
|---------------|-----------------:|
| weekCount     | 1                |
| startWeek     | nuvarande vecka  |
| classes       | alla             |

Notera att det i funktionshuvudet ter sig som att standardvärderna
inte matchar de som angivits i ovanstående tabell. Detta beror på att
standardvärdena antas i funktionskroppen då funktionsanrop inte är
tillåtet i argumentdeklarationen <sup>[3](#PHP-args)</sup>.

### getEventCalendar
Det grafiska gränssnittet hämtas med denna funktion. Den returnerar en
textsträng innehållande HTML-koden för kalendern. Argumenten motsvarar
de till *getEvents*-metoden med undantaget att *$weekCount* inte kan
utelämnas.

## Grafiskt gränssitt till kalendern
Det grafiska gränssnittet är byggt i HTML, CSS samt en liten mängd
JavaScript. All CSS (bortsätt från en ikon) är byggd utan ramverk
(detta gäller endast för kalendern, inte resterande sida).

Kalendern är en HTML-tabell. Varja dag är en <td>-tagg. Innuti denna
ligger händelserna som 

# index.php

## .htaccess
Alla HTTP-frågor (med vissa undantag) dirigeras till index.php på
grund av .htacces-filen. Om detta inte fungerar kan servern behöva
omkonfigureras. En Apache-modul kan behövas anges som aktiv.

## Materialize
Många HTML/CSS/JavaScript-komponenter på sidan skapas med hjälp av ett
ramverk som heter *Materialize* och är baserat på Googles Material
Design <sup>[4](#material-design)</sup>. Ramverket är tillgängligt
på [materializecss.com](http://materializecss.com/).

# Meny
Navigationsmenyn består av både statiskt och dynamiskt innehåll. De
statiska elementen är för att navigera till informationssidorna samt
start. De olika klassernas kalendrar genereras dynamiskt beroende på
innehållet i tabellen *classes* i databasen. Materialize används för
layout samt det skript som tar fram och döljer menyn. Knappen som tar
fram menyn fungerar med hjälp av ett skript som finns i *js/script.js*.

# Lärarinformation
Lärarinformationen är skriven som en enkel HTML-fil med tillhörande
CSS som använder sig av Materialize. För att redigera informationen
ändras texten i funktionen *getTeacherInfoContent* i filen *teacherinfo.php*.

# Klocka
Klockan som visas i övre högra hörnet drivs av JavaScript. Skriptet
utnyttjar en
[Date](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date)-instans
för att hämta tiden från klientens dator. En *formatTime*-funktion
används för att lägga till nollor så att tidsformatet HH:MM:SS
fås. *setTimeOut* används för att uppdatera klockan. Den grafiska
visningen sker genom att ändra inre HTML för ett element med id "clock".

# Den här filen
*README*-filer har som uppgift att ge användare viktig
information. Ofta har de (som i det här fallet) filändelsen *.md*. Det
innebär att de är skrivna i typsättningsspråket *Markdown*. Den här
filen är skriven i
[GitHub Flavored Markdown](https://help.github.com/articles/about-writing-and-formatting-on-github/).  

Om filen öppnas i en vanlig textredigerare kommer rubriker föregås med
#, *kursiv text kommer vara omgiven av asterixer* osv. Detta beroro pa
att Markdown-filer är tänkta att konverteras till andra format. Detta
sker automatiskt på sidor som GitHub. Den här filen bör läsas på
projektets [GitHub-sida](https://www.google.se) (repository).  

# Utvecklare
Projektet utvecklades av Lukas Skystedt i kursen
Webbserverprogrammering 1 år 2017.  

# Förslag på fortsatt arbete
- Anpassa sidan för mobila enheter.  
- Integrera schemat från novasoftware
- 


---
<a name="ical-time">[1]</a> [Kanzaki, ical DateTime](http://www.kanzaki.com/docs/ical/dateTime.html)  
<a name="MySQL-time">[2]</a> [MySQL.com, Date and Time formats](https://dev.mysql.com/doc/refman/5.7/en/date-and-time-types.html)  
<a name="PHP-args">[3]</a> [PHP Manual, Default Arguments](http://php.net/manual/en/functions.arguments.php)  
<a name="material-design">[4]</a> [Google, Material Design](https://material.io/guidelines/)  
