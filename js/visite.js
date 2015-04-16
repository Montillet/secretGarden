var gt = new Gettext({ 'domain' : 'messages' });
function T_ (msgid) { return gt.gettext(msgid); }

var tour = {
  id: "Secret-Garden",
  steps: [
    {
      title: T_("Bienvenue sur Secret Garden"),
      content: T_("Salut ! Bienvenue sur votre moniteur. C'est depuis ce menu que vous pourrez visualiser les constantes de votre jardin et sélectionner les jauges que vous voulez."),
      target: "title",
      placement: "bottom",
	  xOffset: "center",
      arrowOffset: "center",
      multipage: true,
      onNext: function() {
        window.location = "reglage.php"
      }
    },
    {
      title: T_("Le menu de réglage"),
      content: T_("Sur cette page, vous pouvez modifier le cycle lumineux ainsi que le cycle d'arrosage"),
      target: "hopscotch",
      placement: "bottom",
      xOffset: "center",
      arrowOffset: "center",
	  /*multipage: true,*/
	  onPrev: function() {
        window.location = "moniteur.php"
      }
    },
	{
      content: T_("Ci-dessus, vous pouvez configurer le cycle lumineux"),
      target: "light",
      placement: "bottom",
    },
	{
      content: T_("Ci-dessus, vous pouvez configurer le cycle d'arrosage et son heure de départ "),
      target: "water",
      placement: "bottom",
    },
	{
      content: T_("Ci-dessous, le niveau de sécurité. Il y a 3 différents états que vous pouvez configurer en fonction de vos connaissances en jardinage. Plus d'infos, allez voir la FAQ "),
      target: "security",
      placement: "top",
	  multipage: true,
	  onNext: function() {
        window.location = "statistique.php"
      }
    },
	{
      title: T_("Le menu des statistiques"),
      content: T_("Sur cette page, vous pouvez visualiser toutes les données dans diverses sortes de graphiques."),
      target: "graph",
      placement: "bottom",
	  onPrev: function() {
        window.location = "reglage.php"
      }
    },
	{
      content: T_("C'est ici que vous pouvez choisir quelle sorte de graphique vous voulez "),
      target: "aide_graph",
      placement: "top",
	  multipage: true,
	  onNext: function() {
        window.location = "budget.php"
      }
    },
	{
      title: T_("Ici, vous pouvez visualiser des prévisions de votre budget"),
      content: T_("Sur cette page, vous avez juste à remplir les champs requis et Secret Garden vous donnera une estimation du coût électrique de votre installation"),
      target: "budget",
      placement: "top",
	  onPrev: function() {
        window.location = "statistique.php"
      }
    },
	{
	  title: T_("Vous savez tout !"),
      content: T_("Maintenant, vous avez les clés du succès, il ne vous reste plus qu'à vous lancer et surtout à vous faire plaisir ! "),
      target: "fin",
      placement: "bottom",
	  xOffset: "center",
      arrowOffset: "center"
    }	
  ],
  showPrevButton: true
},

/* ========== */
/* TOUR SETUP */
/* ========== */
/*addClickListener = function(el, fn) {
  if (el.addEventListener) {
    el.addEventListener('click', fn, false);
  }
  else {
    el.attachEvent('onclick', fn);
  }
},
*/
startEl = document.getElementById("tour_on");

if (startEl) {
    if (!hopscotch.isActive) {
      hopscotch.startTour(tour);
    }
}
else {
  // Assuming we're on a page different than dashboard page.
  if (hopscotch.getState() === "Secret-Garden:1") {
    hopscotch.startTour(tour,1);
  }
  else if (hopscotch.getState() === "Secret-Garden:2") {
    hopscotch.startTour(tour,2);
  }
  else if (hopscotch.getState() === "Secret-Garden:3") {
    hopscotch.startTour(tour,3);
  }
  else if (hopscotch.getState() === "Secret-Garden:4") {
    hopscotch.startTour(tour,4);
  }
  else if (hopscotch.getState() === "Secret-Garden:5") {
    hopscotch.startTour(tour,5);
  }
  else if (hopscotch.getState() === "Secret-Garden:6") {
    hopscotch.startTour(tour,6);
  }
  else if (hopscotch.getState() === "Secret-Garden:7") {
    hopscotch.startTour(tour,7);
  }
  else if (hopscotch.getState() === "Secret-Garden:8") {
    hopscotch.startTour(tour,8);
  }
}