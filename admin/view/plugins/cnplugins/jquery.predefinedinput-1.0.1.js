/*
	Predefined Input Values for jQuery
	Copyright (c) 2011-2015 (AkisC comvos.net)
	Version: 1.0.2 (24/04/2015)
	
	usage:
	$('#inputid').iAlpha();				- For International chars
	$('#inputid').iAlphaNumeric();		- For International and digit chars
	$('#inputid').iAlphaGR();			- For International and Greek chars
	$('#inputid').iAlphaNumericGR();	- For International, Greek and digit chars
	$('#inputid').iNumeric();			- For digit chars with dot
	$('#inputid').iInt();				- For digit chars without dot
	
	$('#inputid').iCustom({
		allow:'',
		disallow:'',
		space:true,
		comma:true
	});
*/
//----------------------------------------------------------------------------------------------------------------
jQuery.fn.iCustom = function(options)
{
	var inputOptions = jQuery.extend({allow:'', disallow:'', space:false, comma:false}, options);
	return jQuery(this).keypress (function (e) {					
		
		if (!e.charCode) {
			var code = String.fromCharCode(e.which);
		} else {
			var code = String.fromCharCode(e.charCode);							
		}
		if (inputOptions.space) { inputOptions.allow += ' '; }
		if (inputOptions.comma) { inputOptions.allow += ','; }
		
		if (code && (typeof(e.keyCode) == 'undefined' || (
			e.keyCode != 8 && 		/* backspace */
			e.keyCode != 9 &&		/* tab */
			e.keyCode != 16 &&		/* shift */
			e.keyCode != 35	&&		/* end */
			e.keyCode != 36 &&		/* home */
			e.keyCode != 37	&&		/* arrow left */
			e.keyCode != 38	&&		/* arrow up */
			e.keyCode != 39 &&		/* arrow right */
			e.keyCode != 40 &&		/* arrow down */
			e.keyCode != 46  		/* delete */
			))) {
			if (inputOptions.allow.length != 0 && inputOptions.disallow.length != 0) {
				if(inputOptions.allow.indexOf(code) == -1) {
					e.preventDefault();
				} else if(inputOptions.disallow.indexOf(code) != -1) {
					e.preventDefault();
				}
			} else if(inputOptions.allow.length != 0) {
				if(inputOptions.allow.indexOf(code) == -1) {
					e.preventDefault();
				}
			} else if(inputOptions.disallow.length != 0) {
				if(inputOptions.disallow.indexOf(code) != -1) {
					e.preventDefault();
				}
			}
		}
		
		if (e.ctrlKey && e.keyCode == 86) { e.preventDefault(); }
		$(this).bind('contextmenu',function () {return false});					
	});		
		
};
//----------------------------------------------------------------------------------------------------------------
jQuery.fn.iAlpha = function(options)
{
	var inputOptions = jQuery.extend({allow:'', disallow:'', space:false, comma:false}, options);
	return this.each (function() {
		jQuery(this).iCustom({allow:'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' + inputOptions.allow, disallow:inputOptions.disallow, space:inputOptions.space, comma:inputOptions.comma});
	});		
};
//----------------------------------------------------------------------------------------------------------------
jQuery.fn.iAlphaGR = function(options)
{
	var inputOptions = jQuery.extend({allow:'', disallow:'', space:false, comma:false}, options);
	return this.each (function() {
		jQuery(this).iCustom({allow:'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZαβγδεζηθικλμνξοπρστυφχψωΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩέύίόάήώϊΐΈΎΊΌΆΉΏΪς' + inputOptions.allow, disallow:inputOptions.disallow, space:inputOptions.space, comma:inputOptions.comma});
	});		
};
//----------------------------------------------------------------------------------------------------------------
jQuery.fn.iAlphaNumeric = function(options)
{
	var inputOptions = jQuery.extend({allow:'', disallow:'', space:false, comma:false}, options);
	return this.each (function() {
		jQuery(this).iCustom({allow:'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.' + inputOptions.allow, disallow:inputOptions.disallow, space:inputOptions.space, comma:inputOptions.comma});
	});		
};
//----------------------------------------------------------------------------------------------------------------
jQuery.fn.iAlphaNumericGR = function(options)
{
	var inputOptions = jQuery.extend({allow:'', disallow:'', space:false, comma:false}, options);
	return this.each (function() {
		jQuery(this).iCustom({allow:'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZαβγδεζηθικλμνξοπρστυφχψωΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩέύίόάήώϊΐΈΎΊΌΆΉΏΪς1234567890.' + inputOptions.allow, disallow:inputOptions.disallow, space:inputOptions.space, comma:inputOptions.comma});
	});		
};
//----------------------------------------------------------------------------------------------------------------
jQuery.fn.iNumeric = function(options)
{
	var inputOptions = jQuery.extend({allow:'', disallow:'', space:false, comma:false}, options);
	return this.each (function() {
		jQuery(this).iCustom({allow:'1234567890.' + inputOptions.allow, disallow:inputOptions.disallow, space:inputOptions.space, comma:inputOptions.comma});
	});		
};
//----------------------------------------------------------------------------------------------------------------
jQuery.fn.iInt = function(options)
{
	var inputOptions = jQuery.extend({allow:'', disallow:'', space:false, comma:false}, options);
	return this.each (function() {
		jQuery(this).iCustom({allow:'1234567890' + inputOptions.allow, disallow:inputOptions.disallow, space:inputOptions.space, comma:inputOptions.comma});
	});		
};
//----------------------------------------------------------------------------------------------------------------
