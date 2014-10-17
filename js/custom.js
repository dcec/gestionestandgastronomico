$(function(){
			$('#tavolo').keyboard({
			layout: 'custom',
		  customLayout: {
		   'default' : [
			'1 2 3',
			'4 5 6',
			'7 8 9',
			'0',
			'{accept} {cancel}'
		   ]},
		   position : {
		   my : 'right bottom',
		   at : 'right bottom'
		  }});
		});
		$(function(){
			$('#coperti').keyboard({
			layout: 'custom',
		  customLayout: {
		   'default' : [
			'1 2 3',
			'4 5 6',
			'7 8 9',
			'0',
			'{accept} {cancel}'
		   ]},
		   position : {
		   my : 'right bottom',
		   at : 'right bottom'
		  }});
		});
		$(function(){
			$('#contanti').keyboard({
			layout: 'custom',
		  customLayout: {
		   'default' : [
			'1 2 3',
			'4 5 6',
			'7 8 9',
			'0 ,',
			'{accept} {cancel} {clear}'
		   ]},
		   position : {
		   my : 'right bottom',
		   at : 'right bottom'
		  }});
		});
		function updateInput(ish){
			totale = $("#totale").attr('value');
			contanti.value =  ish ;//+ '\u20ac';
			resto.value =  parseFloat(ish) - parseFloat(totale);//+ '\u20ac'; 
		};
		$(document).ready(
		function(){
			$('.Multiple').jPicker({
				window: // used to define the position of the popup window only useful in binded mode
				{
				position:
				{
				  x: 'screenCenter', // acceptable values "left", "center", "right", "screenCenter", or relative px value
				  y: 'center', // acceptable values "top", "bottom", "center", or relative px value
				}}
				
			});
		});
		function refreshAndClose() {
            window.opener.location.reload(true);
            window.close();
		};
		$(function(){
			$('#nuovo_ordine').keyboard({
			layout: 'custom',
		  customLayout: {
		   'default' : [
			'1 2 3',
			'4 5 6',
			'7 8 9',
			'0',
			'{accept} {cancel}'
		   ]}});
		});
		$(function(){
			$('#recupara_ordine').keyboard({
			layout: 'custom',
		  customLayout: {
		   'default' : [
			'1 2 3',
			'4 5 6',
			'7 8 9',
			'0',
			'{accept} {cancel}'
		   ]}});
		});