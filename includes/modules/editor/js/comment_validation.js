/**
 * @file   modules/editor/js/comment_validation.js
 * @author singleview.co.kr (singleviewcokr@gmail.com)
 * @brief  editor 모듈의 javascript
 **/

/* insert comment */
jQuery.extend(jQuery.validator.messages, {
    required: x2board_locale.lbl_required,
    // remote: "Please fix this field.",
    // email: "Please enter a valid email address.",
    // url: "Please enter a valid URL.",
    // date: "Please enter a valid date.",
    // dateISO: "Please enter a valid date (ISO).",
    // number: "Please enter a valid number.",
    // digits: "Please enter only digits.",
    // creditcard: "Please enter a valid credit card number.",
    // equalTo: "Please enter the same value again.",
    // accept: "Please enter a value with a valid extension.",
    // maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
    // minlength: jQuery.validator.format("Please enter at least {0} characters."),
    // rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
    // range: jQuery.validator.format("Please enter a value between {0} and {1}."),
    // max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
    // min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
});

// https://goodteacher.tistory.com/162
jQuery("#x2board-comment-form").validate({ 
	// rules:{ 
	// 	content: { 
	// 		required: true, 
	// 		rangelength: [2,10] 
	// 	}, 
	// 	email:{ email: true } 
	// }, 
	// messages:{ /* define message object if validation failed */
	// 	nick_name: {
	// 		required: x2board_locale.lbl_required,
	// 		// rangelength: "content short"
	// 	},
	// },
	submitHandler:function(form){
		if(jQuery('iframe').length === 1 && jQuery('iframe').attr('class') === 'cke_wysiwyg_frame cke_reset' ){ // if ckeditor exists
			let s_body_from_iframe = jQuery('iframe').contents().find('body').html();
			if(jQuery(s_body_from_iframe).text().length < 5) {
				alert(x2board_locale.lbl_content + ' ' + x2board_locale.lbl_required);
				return;
			}
			jQuery("input[name=content]").val(s_body_from_iframe);
		}
		form.submit();
	}
});