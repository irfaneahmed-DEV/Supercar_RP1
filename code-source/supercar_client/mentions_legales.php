<?php
function getMentionsLegales(): String
{
    return "
<!-- Popup mentions légales -->
<div id='mentionsLegalesOverlay'>
    <div id='mentionsLegales'>
        <span onclick='closeMentionsLegales()' style='position:absolute; top:15px; right:20px; cursor:pointer; font-size:24px; font-weight:bold'>&times;</span>

        <h2>Mentions légales</h2>

        <p><strong>Nom du site :</strong><br>
            Supercar</p>

        <p><strong>Responsables du projet :</strong><br>
            Ahmed Irfane, HARIFETRA JOHAN RAFANA &amp; Judikael fahendrena Taryl RAZAFIMAHEFA<br>
            Étudiants en BTS SIO – Réalisation Professionnelle<br>
            MCCI Business School<br>
            Rue Savoir, Cybercity, Ebene, Maurice</p>

        <p><strong>Nature du site :</strong><br>
            Supercar est un site web réalisé dans le cadre d'un projet scolaire à la MCCI Business School. Il constitue un prototype pédagogique destiné à mettre en pratique les compétences acquises en développement web et en gestion de bases de données. Il n'a pas vocation à représenter une entreprise commerciale réelle.</p>

        <p><strong>Propriété intellectuelle :</strong><br>
            Les contenus créés dans le cadre du projet, notamment les textes, les éléments graphiques et le code source, sont soumis aux règles applicables en matière de propriété intellectuelle. Les contenus appartenant à des tiers restent la propriété de leurs auteurs ou titulaires respectifs. Toute reproduction ou réutilisation doit respecter les droits applicables et, le cas échéant, obtenir les autorisations nécessaires.</p>

        <p><strong>Données personnelles :</strong><br>
            Le site contient des formulaires permettant notamment d'envoyer des messages et d'effectuer des demandes d'essai de véhicules. Les informations transmises peuvent être enregistrées dans la base de données du site pour assurer le fonctionnement et la démonstration de ses fonctionnalités.<br><br>
            Les données sont utilisées exclusivement dans le cadre du projet scolaire et ne sont pas exploitées à des fins commerciales, publicitaires ou marketing. Leur traitement doit respecter la législation applicable en matière de protection des données personnelles.<br><br>
            Pour plus d'informations sur la collecte, l'utilisation et la conservation des données, les utilisateurs peuvent consulter la politique de confidentialité du site.</p>

        <p><strong>Limitation de responsabilité :</strong><br>
            Ce site est un prototype académique réalisé dans le cadre d'un projet étudiant. Les informations relatives aux véhicules et aux services sont présentées à titre pédagogique et illustratif. Elles ne constituent pas nécessairement des offres commerciales réelles. Les responsables du projet s'efforcent de maintenir un fonctionnement correct du site, sans pouvoir garantir une disponibilité permanente ni l'absence d'erreurs.</p>

        <p><strong>Cookies et traceurs :</strong><br>
            Le site peut utiliser des cookies ou des technologies similaires nécessaires à son fonctionnement, notamment pour la gestion des sessions utilisateur. Aucun outil de suivi publicitaire n'est prévu dans le cadre du projet. Les technologies effectivement utilisées doivent être conformes à la législation applicable.</p>

        <p><strong>Contact :</strong><br>
            Pour toute question concernant le site Supercar, son fonctionnement ou le traitement des données personnelles, vous pouvez contacter l'équipe du projet par l'intermédiaire de la MCCI Business School.</p>
    </div>
</div>
    ";
}


function getPolitiqueDeConfidentialite(): String
{
    return "
    
    <div id='ConfidentialiteOverlay'>
    <div id='Confidentialite'>
        <span onclick='closeCondition()' style='position:absolute; top:15px; right:20px; cursor:pointer; font-size:24px; font-weight:bold'>&times;</span>

        <h2>Politique de confidentialité</h2>

        <p><strong>Introduction</strong><br>
            Cette politique de confidentialité s'applique au site Supercar, réalisé dans le cadre d'un projet scolaire à la MCCI Business School. Elle a pour objectif d'informer les visiteurs sur la collecte, l'utilisation et la protection des informations qu'ils peuvent transmettre lorsqu'ils utilisent le site.</p>

        <p><strong>Collecte des données</strong><br>
            Le site Supercar contient des formulaires permettant aux utilisateurs de transmettre certaines informations, notamment pour envoyer un message ou effectuer une demande d'essai de véhicule.<br><br>
            Les informations saisies dans ces formulaires peuvent inclure des données personnelles, telles que le nom, le prénom, l'adresse e-mail, le numéro de téléphone ou toute autre information nécessaire au fonctionnement des fonctionnalités proposées.</p>

        <p><strong>Utilisation des données</strong><br>
            Les informations recueillies sont utilisées uniquement dans le cadre du fonctionnement et de la démonstration des fonctionnalités du projet scolaire Supercar.<br><br>
            Les données ne sont pas utilisées à des fins commerciales, publicitaires ou marketing. Elles ne sont pas destinées à être exploitées pour promouvoir ou vendre des produits ou des services.</p>

        <p><strong>Conservation des données</strong><br>
            Les données transmises peuvent être enregistrées dans la base de données du site afin de permettre le fonctionnement des formulaires et des fonctionnalités de gestion.<br><br>
            Dans le cadre de ce projet scolaire, les données doivent être conservées uniquement pendant la durée nécessaire à la réalisation et à l'évaluation du projet, puis supprimées lorsqu'elles ne sont plus nécessaires, sous réserve des obligations légales applicables.</p>

        <p><strong>Cookies et technologies similaires</strong><br>
            Le site peut utiliser des cookies ou des technologies similaires nécessaires à son fonctionnement, notamment pour la gestion des sessions utilisateur.<br><br>
            Aucun outil de suivi publicitaire n'est prévu dans le cadre du projet. Si des cookies ou des services supplémentaires sont ajoutés, cette politique devra être mise à jour en conséquence.</p>

        <p><strong>Partage des informations</strong><br>
            Les informations recueillies ne sont pas destinées à être vendues ni utilisées à des fins commerciales.<br><br>
            Elles peuvent être accessibles aux membres autorisés de l'équipe de projet ou aux personnes chargées de son encadrement, dans la mesure nécessaire au développement, au test et à l'évaluation du site.</p>

        <p><strong>Sécurité des données</strong><br>
            Des mesures de sécurité adaptées doivent être mises en place afin de protéger les informations enregistrées contre les accès non autorisés, la perte, la modification ou la divulgation.<br><br>
            Toutefois, aucun système informatique ne pouvant garantir une sécurité absolue, les utilisateurs sont invités à ne transmettre que les informations nécessaires à l'utilisation des formulaires.</p>

        <p><strong>Vos droits</strong><br>
            Conformément aux règles applicables en matière de protection des données personnelles, les utilisateurs peuvent, selon les conditions prévues par la législation applicable, demander l'accès à leurs données, leur rectification ou leur suppression, ainsi que s'opposer à certains traitements ou en demander la limitation.<br><br>
            Pour toute demande concernant les données personnelles transmises au site, les utilisateurs peuvent contacter l'équipe du projet par l'intermédiaire de l'établissement scolaire.</p>

        <p><strong>Modifications de cette politique</strong><br>
            Cette politique de confidentialité peut être mise à jour afin de refléter les modifications apportées au site, à ses fonctionnalités ou aux règles applicables. Toute nouvelle version sera publiée sur cette page.</p>

        <p><strong>Contact</strong><br>
            Pour toute question relative à cette politique de confidentialité ou au traitement des données personnelles dans le cadre du projet Supercar, vous pouvez contacter l'équipe du projet par l'intermédiaire de la MCCI Business School.</p>
    </div>
</div>
    
    ";
}
