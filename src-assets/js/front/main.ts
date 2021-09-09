(window as any).pixCopyText = function ( value: string, id: string ) 
{
	if ( navigator.clipboard )
	{
		navigator
			.clipboard
			.writeText(value)
			.then(() => { (window as any).pixCopied(id); })
			.catch(() => { (window as any).pixCopyFallback(value, id); });
	}
	else
	{ (window as any).pixCopyFallback(value, id); }
};

(window as any).pixCopyFallback = function ( value: string, id: string )
{
	const el = document.createElement('textarea');
	
	el.value = value;
	el.setAttribute('readonly', '');
	el.style.position = 'absolute';
	el.style.left = '-9999px';
	document.body.appendChild(el);
	
	el.select();  
	el.setSelectionRange(0, 99999);

	const copy = document.execCommand('copy');
	document.body.removeChild(el);

	if ( copy )
	{ (window as any).pixCopied(id); }

	if ( window.getSelection() !== null && window.getSelection() !== undefined )
	{ (window.getSelection() as any).removeAllRanges(); }
};

(window as any).pixCopied = function (id: string) {
	const el: any = document.getElementById(id);
	const cn = el.innerHTML;
	el.innerHTML = 'Copiado';

	setTimeout(() => { el.innerHTML = cn; }, 1500);
};