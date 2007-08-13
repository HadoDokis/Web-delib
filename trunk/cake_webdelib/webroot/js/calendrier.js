//créer un object date (sans paramètres => today), qui sera utilisé dans getHTMLDay, show_calendar
var gNow = new Date();  

//variable globale - la fenêtre du calendrier
var ggWinCal;

//FONCTIONS POUR RETOURNER LES MOIS / ANNEES PRECEDENTES ET SUIVANTES
function getNextMonth (iMonth, iYear) {
	if (iMonth == 11) return 0; else return parseInt(iMonth) + 1;
}
function getNextYear (iMonth, iYear) {
	if (iMonth == 11) return parseInt(iYear) + 1; else return iYear;
}
function getPrevMonth (iMonth, iYear) {
	if (iMonth == 0) return 11; else return parseInt(iMonth) - 1;
}
function getPrevYear (iMonth, iYear) {
	if (iMonth == 0) return parseInt(iYear) - 1; else return iYear;
}
//CONSTRUCTEUR DE L'OBJET CALENDAR
function Calendar(iTextBox, iWinCal, iMonth, iYear, iLanguage, iSubmit) {
	if ((iMonth == null) && (iYear == null)) return;
	if (iWinCal == null) this.gWinCal = ggWinCal; else this.gWinCal = iWinCal;
	//customise your calender here:
	this.gWeekendDays = [5,6]; 												//les jours du weekend (NB: lundi=0, dim=6)
	this.gBorderWidth = 1;													//largeur des bordures
	//images
	this.gGoGo = ">>";					//image ">>" ou texte
	this.gGo = ">";						//image ">" ou texte
	this.gBack = "<";					//image "<" ou texte
	this.gBackBack = "<<";			//image "<<" ou texte
	this.gShim = "images/shim.gif";											//image shim (1px transparent)
	//styles
	var vStyle ="";
	vStyle += "<STYLE type=\"text/css\">";
	vStyle += "A{text-decoration:none;color:#000000}\n";								//liens
	vStyle += "BODY{font-family:Arial,Helvetica,sans-serif; font-size:14px}\n";			//Tout texte en dehors d'un tableau
	vStyle += "TD{font-size:12px; text-align:center; background-color:#FFFFFF}\n";		//Tout ce qui se trouve dans un tableau	
	vStyle += ".Today{color:#660066; font-weight:bold}\n";								//La date du jour ...
	vStyle += ".Weekend{color:#000000; background-color:#EEEEEE}\n";					//Weekend
	vStyle += ".Header{color:#369; font-weight:bold; background-color:#f2f3f5}\n";	//Headers (jours de la sem.)
	vStyle += ".InactiveDate{color:#999999}\n";											//Dates à la fin du mois suivant
	vStyle += ".Border{background-color:#369}\n";
	vStyle += "</STYLE>";
	this.gStyle = vStyle;
	//autres ...
	this.gYear = iYear;
	this.gLanguage = iLanguage;									//langue - changer dans l'appel de la fonction depuis la page
	this.gMonthName = this.getMonth(iMonth);  					//Nom du mois (en haut)
	this.gMonth = new Number(iMonth);							//"id" du mois
	this.gTextBox = iTextBox;  									//textbox
	this.gSubmit = iSubmit;										//est-ce qu'on soumet le formulaire? (1=oui, 0=non)
	this.gTitle = eval('Calendar.' + this.gLanguage + 'Title;');  //Title de la fenêtre
}
// This is for compatibility with Navigator 3, we have to create and discard one object before the prototype object exists.
new Calendar();

// Tableau avec les mois de l'année - pour l'affichage du mois en haut du calendrier
Calendar.fMonths = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
Calendar.eMonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
// Tableau des jours de la semaine - pour les titres dans le calendrier
Calendar.fDays = ["lun", "mar", "mer", "jeu", "ven", "sam", "dim"];
Calendar.eDays = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
// Nombre de jours par mois
Calendar.Mois = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
// Title de la fenêtre
Calendar.eTitle = "Calendar";
Calendar.fTitle = "Calendrier";

//Méthodes - elles sont héritées automatiquement, à chaque fois qu'une nouvelle instance d'un objet est créée 
//par contre, les méthodes n'existent qu'une fois dans la mémoire de l'ordinateur.

//METHODE getMonth - cherche le nom du mois dans le tableau des mois
Calendar.prototype.getMonth = function(iMonthNo) {
	var vMonth = eval('Calendar.' + this.gLanguage + 'Months[' + iMonthNo + '];');
	return vMonth;
}
//METHODE wwrite() writeline pour l'objet calendrier
Calendar.prototype.wwrite = function(wtext) {
	this.gWinCal.document.writeln(wtext);
}
//show - MET ENSEMBLE TOUT LE CODE DU CALENDRIER, ET ECRIT TOUT A LA PAGE / FENETRE
Calendar.prototype.show = function() {
	var vCode = "";
	this.gWinCal.document.open();
	this.wwrite(this.getHTMLStartPage()); 	// Get début de la page
	this.wwrite(this.getHTMLNavigation()); 	// Get the code for the navigation bar ... 
	this.wwrite(this.getHTMLCalendar()); 	// Get the complete calendar code for the month ...
	this.wwrite(this.getHTMLEndPage());	// Terminer la page correctement
	this.gWinCal.document.close();
}
//METHODE getHTMLStartPage - DEBUT DE LA PAGE
Calendar.prototype.getHTMLStartPage = function() {
	var vHTML = "";
	// Setup the page...
	vHTML += "<html>";
	vHTML += "<head><title>" + this.gTitle + "</title>";
	vHTML += "</head>";
	vHTML += this.gStyle;  //feuille de style
	vHTML += "<body>";
	vHTML += "<B>";
	vHTML += this.gMonthName + " " + this.gYear;
	vHTML += "</B>";
	vHTML += "<table cellspacing='0' cellpadding='0' height='3'><tr><td><img src='" + this.gShim + "' height='3'></td></tr></table>";
	return vHTML;
}
//METHODE getHTMLNavButtons - CREE LES BOUTONS "BACK", "PREV" etc.
Calendar.prototype.getHTMLNavButtons = function(iMonth, iYear, iImage) {
	return  "<TD class='Header' width='25%'><A HREF=\"" +
		"javascript:window.opener.Build(" + 
		"'" + this.gTextBox + "', '" + iMonth + "', '" + iYear + "', '" + this.gLanguage + "', " + this.gSubmit + 
		");" +
		"\">" + iImage + "<\/A></TD>";
}
//METHODE getHTMLNavigation - BARRE DE NAVIGATION
Calendar.prototype.getHTMLNavigation = function() {
	var vHTML = "";
	vHTML += "<TABLE WIDTH='220' BORDER=0 CELLSPACING=" + this.gBorderWidth + " CELLPADDING=0 class='Border'><TR><TD>";
	vHTML += "<TABLE WIDTH='220' BORDER=0 CELLSPACING=0 CELLPADDING=1><TR>";
	vHTML += this.getHTMLNavButtons(this.gMonth, (parseInt(this.gYear)-1), this.gBackBack);
	vHTML += this.getHTMLNavButtons(getPrevMonth(this.gMonth,this.gYear), getPrevYear(this.gMonth,this.gYear), this.gBack);
	//vHTML += "<TD>[<A HREF=\"javascript:window.print();\">Print</A>]</TD>";  //remettre pour "print" - il faut aussi modifier largeur ci-dessus
	vHTML += this.getHTMLNavButtons(getNextMonth(this.gMonth,this.gYear), getNextYear(this.gMonth,this.gYear), this.gGo);
	vHTML += this.getHTMLNavButtons(this.gMonth, (parseInt(this.gYear)+1), this.gGoGo);
	vHTML += "</TR></TABLE>";
	vHTML += "</tr></td></TABLE>";
	vHTML += "<table cellspacing='0' cellpadding='0' height='9'><tr><td><img src='" + this.gShim + "' height='3'></td></tr></table>";
	return vHTML;
}
//METHODE getDay - cherche le nom du jour de la semaine dans le tableaux des jours
Calendar.prototype.getDay = function(iDayNo) {
	var vDay = eval('Calendar.' + this.gLanguage + 'Days[' + iDayNo + '];');
	return vDay;
}
Calendar.prototype.getHTMLDayHeader = function(iDay,iWeekend) {
	var vDay = this.getDay(iDay);
	// 7*14% = 98% donc on ajoute 1% en largeur pour le weekend
	if (iWeekend) {
		return ("<TD Width='15%' class='Header'>" + vDay + "</TD>");
	} else {
		return ("<TD Width='14%' class='Header'>" + vDay + "</TD>");
	}		
}
//getHTMLCalHeader() : Header du calendrier même avec les jours
Calendar.prototype.getHTMLCalHeader = function() {
	var vCode = "";
	vCode += "<TR>";
	vCode += this.getHTMLDayHeader(0) + this.getHTMLDayHeader(1) + this.getHTMLDayHeader(2) + this.getHTMLDayHeader(3);
	vCode += this.getHTMLDayHeader(4) + this.getHTMLDayHeader(5,1) + this.getHTMLDayHeader(6,1);
	vCode += "</TR>";
	//ligne entre les headers et les dates
	vCode += "<TR><TD colspan='7' class='Border' height='" + this.gBorderWidth + "' class='Header'><img src='" + this.gShim + "' height='" + this.gBorderWidth + "'></TD></TR>";
	return vCode;
}
//METHODE getHTMLDay() : pour mettre aujourd'hui en rouge
Calendar.prototype.getHTMLDay = function(iDay) {
	if (iDay == gNow.getDate() && this.gMonth == gNow.getMonth() && this.gYear == gNow.getFullYear())
		return ("<div class='Today'>" + iDay + "</div>");
	else
		return (iDay);
}
//METHODE getHTMLWeekend() - pour mettre le weekend en gris
Calendar.prototype.getHTMLWeekend = function(iDay) {
	var i;
	for (i=0; i<this.gWeekendDays.length; i++) {//>
		if (iDay == this.gWeekendDays[i])
			return (" class='Weekend'");
	}
	return "";
}
//METHODE getDaysInMonth - pour un mois et une ann´e donn´s
Calendar.prototype.getDaysInMonth = function(iMonthNo, iYear) {
	if (((iYear % 4 == 0 && iYear % 100 != 0 ) || (iYear % 400 == 0 )) && iMonthNo == 1) {  //bissextile et f´vrier
		return 29;
	}else{
		return Calendar.Mois[iMonthNo];
	}
}
//METHODE getDateFormat - formattage de la date à retourner ...
Calendar.prototype.getDateFormat = function(iDay) {
	var vData;
	var vMonth = 1 + this.gMonth;
	vMonth = (vMonth.toString().length < 2) ? "0" + vMonth : vMonth;//>
	var vY4 = new String(this.gYear);
	var vDD = (iDay.toString().length < 2) ? "0" + iDay : iDay;//>
	vData = vDD + "\/" + vMonth + "\/" + vY4;  //Date avec "/"
	return vData;
	/*
	Utiliser les lignes suivantes pour modifier le format de la date retourn´e
	//var vMon = this.getMonth(this.gMonth).substr(0,3).toUpperCase();
	//var vFMon = this.getMonth(this.gMonth).toUpperCase();
	//var vY2 = new String(this.gYear.substr(2,2));
	//vData = vDD + "\." + vMonth + "\." + vY4;  //Date avec "."
	*/
}
//METHODE getHTMLDateCell
Calendar.prototype.getHTMLDateCell = function(iDay) {
	var strSubmit = "";
	if (this.gSubmit == 1) {
		strSubmit = "self.opener.document." + this.gTextBox + ".form.submit();"  //soumet le form contenant le champ gTextBox
	}
	return	"<TD" + this.getHTMLWeekend(j) + ">" + 
				"<A HREF='#' " + 
					"onClick=\"self.opener.document." + this.gTextBox + ".value='" + 
					this.getDateFormat(iDay) + 
					"';window.close();" + strSubmit + "\">" + 
					this.getHTMLDay(iDay) + 
				"</A>" + 
			"</TD>";
}
//METHODE getHTMLCalWeeks - crée la partie calendrier avec tous les jours du mois (le calendrier même)
Calendar.prototype.getHTMLCalWeeks = function() {
	var vDate = new Date();
	vDate.setDate(1);
	vDate.setMonth(this.gMonth);
	vDate.setFullYear(this.gYear);
	var vFirstDay=vDate.getDay()-1;   //on fait -1 parce que sinon dimanche serait le premier jour de la semaine, et pas lundi!
	if (vFirstDay==-1) vFirstDay=6;	  //on fait un shift de 1 position ... (modulo) => lundi=0, mardi=1, etc
	var vDay=1;
	var vLastDay=this.getDaysInMonth(this.gMonth, this.gYear);
	var vOnLastDay=0;
	var vCode = "";
	//----Première semaine
	//il faut en mettre jusqu'au 1er du mois
	vCode = vCode + "<TR>";
	for (i=0; i<vFirstDay; i++) {
		vCode = vCode + "<TD" + this.getHTMLWeekend(i) + ">&nbsp;</TD>";
	}
	// Write rest of the 1st week (on s'arrête à dimanche avec j<7)
	for (j=vFirstDay; j<7; j++) {
		vCode += this.getHTMLDateCell(vDay)
		vDay=vDay + 1;
	}
	vCode = vCode + "</TR>";
	//----autres semaines
	for (k=2; k<7; k++) {
		vCode = vCode + "<TR>";
		for (j=0; j<7; j++) {
			vCode += this.getHTMLDateCell(vDay)
			vDay=vDay + 1;
			if (vDay > vLastDay) {
				vOnLastDay = 1;
				break;
			}
		}
		if (j == 6)
			vCode = vCode + "</TR>";
		if (vOnLastDay == 1)
			break;
	}
	//----Fill up the rest of last week with proper blanks, so that we get proper square blocks
	for (m=1; m<(7-j); m++) {
		if (this.gYearly)
			vCode = vCode + "<TD WIDTH='14%'" + this.getHTMLWeekend(j+m) + 
			"></TD>";
		else
			vCode = vCode + "<TD WIDTH='14%'" + this.getHTMLWeekend(j+m) + 
			"><div class='InactiveDate'>" + m + "</div></TD>";
	}
	return vCode;
}
//METHODE getHTMLCalendar - MET ENSEMBLE LE CODE HTML POUR LE CALENDRIER MEME (TABLEAUX & HEADERS & DATA)
Calendar.prototype.getHTMLCalendar = function() {
	//c'est deux tableaux, l'un dans l'autre pour créer une bordure colorée
	var vBeginTable = "<TABLE width=220 BORDER=0 cellspacing=0 cellpadding=" + this.gBorderWidth + "><tr><td class='Border'><TABLE width=220 cellspacing=0 cellpadding=0 BORDER=0>";
	var vHeader_Code = this.getHTMLCalHeader();			//Ligne avec lun, mar, mer, jeu etc.
	var vData_Code = this.getHTMLCalWeeks();				//Le calendrier même
	var vEndTable = "</TABLE></td></tr></TABLE>";
	return vBeginTable + vHeader_Code + vData_Code + vEndTable;
}
//METHODE getHTMLEndPage - FIN DE LA PAGE
Calendar.prototype.getHTMLEndPage = function() {
	return "</body></html>";
}
//FUNCTION Build - utilise le constructeur pour construire la fonction et appelle la fonction pour l'afficher
function Build(iTextBox, iMonth, iYear, iLanguage, iSubmit) {
	gCal = new Calendar(iTextBox, ggWinCal, iMonth, iYear, iLanguage, iSubmit);
	gCal.show();
}
//FUNCTION show_calendar - appelée depuis la page
function show_calendar() {
/* 	Le tableau "arguments" contient les paramètres de l'appel de cette fonction (dans la page elle-même)
	p_item	: (obligatoire)  		Objet - champ texte où il faut rentrer la date (ex. document.form1.txtDate)
	p_language : (obligatoire)		langue - "en" = English, "f" = francais (tableaux eMonths, eDays ...)
	p_submit: (non-obligatoire)		1 = submit le formulaire dès qu'on a rentré une valeur, autrement pas de submit
	p_month : (non-obligatoire) 		Mois sur lequel s'ouvre le calendrier
	p_year	: (non-obligatoire)		Année sur laquelle s'ouvre le calendrier    */

	p_item = arguments[0];
	p_language = arguments[1];
	
	if (arguments[2] != 1) p_submit = 0; else p_submit = 1;
	if (arguments[3] == null) p_month = new String(gNow.getMonth()); else p_month = arguments[3];
	if (arguments[4] == "" || arguments[4] == null)	p_year = new String(gNow.getFullYear().toString()); else p_year = arguments[4];

	//créer nouvelle fenêtre
	ggWinCal = window.open("", "Calendar", "width=240,height=188,status=no,resizable=no,top=200,left=200");  //dims correct pour IE Mac, changer pour autres browsers
	ggWinCal.opener = self;

	Build(p_item, p_month, p_year, p_language, p_submit);
}
