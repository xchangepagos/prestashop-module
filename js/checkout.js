self.name = "ventanaprincipal";
var h = 550;
var w = 450;
var t = (screen.height/2)-(h/2);
var l = (screen.width/2)-(w/2);
function submitPayBox(){
document.getElementById("PayBox").method = "GET";
document.getElementById("PayBox").action = "https://paybox.xchange.ec/prestashop.php";
document.PayBox.target = "myPayBox";
window.open('','myPayBox','width='+w+',height='+h+',left='+l+',top='+t+',location=no');
document.PayBox.submit();
};
