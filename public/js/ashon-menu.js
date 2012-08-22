/*Helper scripts*/
function showlayer(inl)
{
	var myLayer = document.getElementById(inl);
	if(myLayer.style.display=="none" || myLayer.style.display=="")
	{
		myLayer.style.display="block";
	} 
	else 
	{ 
		myLayer.style.display="none";
	}
}
