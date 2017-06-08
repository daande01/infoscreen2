<?php
 function getTeacherInfoCSSLink(){
     return '<link href="css/teacherinfo.css" rel="stylesheet"  type="text/css">';
 }

 function getTeacherInfoContent(){
 ?>
    <div id="info-content" class="container">    
        <p id="intro-p"><span>M</span>&aring;let med den h&auml;r webbsidan &auml;r att alla teknikprogrammets h&auml;ndelser skall kunna samlas p&aring; ett &ouml;versk&aring;dligt s&auml;tt utan att l&auml;gga till on&ouml;digt arbete f&ouml;r l&auml;rare och elever. I menyn till v&auml;nster finns kalendrar f&ouml;r de olika klasserna samt f&ouml;r hela teknikprogrammet. Som l&auml;rare kan du l&auml;gga till h&auml;ndelser som prov, studiebes&ouml;k och andra viktiga saker som elever och l&auml;rare beh&ouml;ver k&auml;nna till. Nedan f&ouml;ljer en instruktion om hur du g&aring;r till v&auml;ga, utan att l&auml;mna itslearning!</p>
        <hr>
        <h2>L&auml;gg till en kurs</h2>
        <p>All interaktion med systemet sker via <b>itslearning</b>. H&auml;ndelser l&auml;ggs till i den kurs som den ing&aring;r i.</p> 
        <p> Det finns ett konto p&aring; itslearning som heter <b>[namn]</b>. Om detta &auml;r medlem i en kurs kommer alla h&auml;ndelser i den kursen att visas p&aring; den h&auml;r sidan.</p>
        <h3>L&auml;gga till [namn]-kontot i en kurs</h3>
        <ul>
            <li>G&aring; in p&aring; kursen som kontot skall bli medlem i.</li>
            <li>Tryck p&aring; <b>Deltagare</b> i menyn p&aring; v&auml;nster sida.</li>
            <li>V&auml;lj <b>L&auml;gg till</b> (gr&ouml;n knapp).</li>
            <li>Skriv in [namn] i f&auml;ltet <i>F&ouml;rnamn</i> och [namn] i f&auml;ltet <i>Efternamn</i>.</li>
            <li>Klicka p&aring; s&ouml;k.</li>
            <li>Kryssa f&ouml;r [namn] i listan.</li>
            <li>V&auml;lj rollen <b>G&auml;st</b> och klicka p&aring; l&auml;gg till.</li>
        </ul>
        <h2>L&auml;gg till en h&auml;ndelse</h2>
        <ul>
            <li>Navigera till kursens info-panel (startsidan). </li>
            <li>Leta efter panelen <b><i>H&auml;ndelser</i></b>. Den finns troligtvis i nedre h&ouml;gra h&ouml;rnet.</li>
            <li>Tryck p&aring; <b><i>L&auml;gg till h&auml;ndelse</i></b>.</li>
            <li>V&auml;lj datum och tid f&ouml;r h&auml;ndelsen.</li>
            <li>Skriv en kort beskrivning av h&auml;ndelsen i textf&auml;ltet. Avancerade funktioner som tabeller, ekvationer och olika typsnitt kommer inte att visas korrekt.</li>
            <li>F&ouml;r att kalendern skall veta vilken eller vilka klass(er) som h&auml;ndelsen ber&ouml;r m&aring;ste antingen <b>kursnamnet eller h&auml;ndelsebeskrivningen</b> inneh&aring;lla namnet p&aring; klassen. Se <i>M&ouml;jliga problem</i> f&ouml;r information om hur du byter kursnamn.</li>
        </ul>
        <p>Nedan f&ouml;ljer n&aring;gra exempel p&aring; h&auml;ndelsebeskrivningar.</p>
        <p class="indent"><b>En klass</b></p>
        <div class="example">Fysikprov kap. 1 f&ouml;r TE2b.</div>
        <p class="indent"><b>Tv&aring; klasser</b></p>
        <div class="example">TE3a och TE3b, opponering GY-arbete.</div>
        <p class="indent"><b>Om kursnamnet inneh&aring;ller klassnamnet (t.ex. <i>16SVESVE03TE3a</i>)</b></p>
        <div class="example">Prov litteraturhistoria.</div>
        <p><b>Det kan ta ett tag innan webbsidan uppdateras, s&auml;rskilt sk&auml;rmen i korridoren.</b></p>
        <h2>Redigera eller ta bort h&auml;ndelser</h2>
        <ul>
            <li>Fr&aring;n kursinfopanelen, klicka p&aring; den h&auml;ndelse som skall redigeras. Detta kommer att ta dig till kalendern. Alternativt kan du komma &aring;t kalendern fr&aring;n menyn &ouml;verst p&aring; itslearning.</li>
            <li>V&auml;lj den h&auml;ndelse som skall redigeras eller tas bort och klicka p&aring; den knapp som motsvarar respektive handling.</li>
        </ul>
        <h2>M&ouml;jliga problem</h2>
        <h3>H&auml;ndelsepanelen finns inte p&aring; kursinfopanelen</h3>
        <ul>
            <li>Klicka p&aring; knappen med tre punkter i &ouml;vre h&ouml;gra h&ouml;rnet.</li>
            <li>V&auml;lj <b><i>L&auml;gg till inneh&aring;llsblock</i></b>.</li>
            <li>Under rubriken <b><i>Samlade</i></b>, v&auml;lj <b><i>H&auml;ndelser</i></b>.</li>
        </ul>
        <h3>H&auml;ndelsen f&auml;rgas inte efter klassnamnet</h3>
        <p>Kontrollera att kursnamnet eller h&auml;ndelsebeskrivningen inneh&aring;ller klassnamnet. Klassnamnet f&aring;r inte inneh&aring;lla blankslag.</p>
        <h4>Exempel p&aring; kursnamn d&auml;r klassen/klasserna kan utl&auml;sas</h4>
        <div class="example">Engelska6TE2b17</div>
        <div class="example">TE1aENGENG06</div>
        <h4> Exempel p&aring; h&auml;ndelsebeskrivningar d&auml;r klassen/klasserna kan utl&auml;sas</h4>
        <div class="example">B&auml;rplockning f&ouml;r TE3b och regndans f&ouml;r TE1a, TE1b och TE2a.</div>
        <div class="example">Prov differentialekvationer (TE3aTE3b).</div>
        <h3>Kursnamnet inneh&aring;ller inte klassnamnet</h3>
        <p>Kom ih&aring;g att det inte &auml;r n&ouml;dv&auml;ndigt f&ouml;r kursnamnet att inneh&aring;lla klassnamnet s&aring; l&auml;nge alla h&auml;ndelsebeskrivningar inneh&aring;ller klassnamnet.</p>
        <h4&Auml;ndra kursnamn</h4>
        <ul>
            <li>G&aring; till kursinfopanelen.</li>
            <li>Tryck p&aring; inst&auml;llningar (<i class="material-icon">settings</i>).</li>
            <li>V&auml;lj <b>Kursegenskaper och -funktioner</b>.</li>
            <li>&Auml;ndra texten i f&auml;ltet <i>Kursnamn</i>. F&auml;ltet <i>Kurskod</i> fyller ingen funktion f&ouml;r den h&auml;r sidan.</li>
            <li>Tryck p&aring; den gr&ouml;na spara-knappen</li>
        </ul>
        <hr>
        <p>F&ouml;r mer information, kontakta ansvarig l&auml;rare: <a href="mailto:daniel.andersson@kunskapsforbundet.se">Daniel Andersson.</p>
    </div>
<?php
}
