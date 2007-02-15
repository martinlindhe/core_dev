// JavaScript Document

elems = document.getElementsByTagName("object");
for(i=0;i<elems.length;i++)
{
    elems[i].outerHTML = elems[i].outerHTML;
}
elems = document.getElementsByTagName("embed");
for(i=0;i<elems.length;i++)
{
    elems[i].outerHTML = elems[i].outerHTML;
}
elems = document.getElementsByTagName("applet");
for(i=0;i<elems.length;i++)
{
    elems[i].outerHTML = elems[i].outerHTML;
}
