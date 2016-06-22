function open_Servired_window(){
	var iWinState = 1;
	var objForm = null;

	if(document.Servired)
		objForm = document.Servired;
	else
		objForm = document.getElementById("Servired");

	if(objForm.windowstate)
	iWinState = objForm.windowstate.value;

	if (iWinState == "1") {
		//popup window
		var serviwin = window.open("","Servired_window","height=600,width=525,menubar=0,resizable=1,scrollbars=1,status=1,titlebar=0,toolbar=0,left=100,top=50");

		if (serviwin)
			serviwin.focus();

		objForm.target = "Servired_window";
	} else
		objForm.target = "";

	objForm.submit();
}