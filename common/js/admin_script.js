/**
 * @author https://singleview.co.kr
 */

/* 관리자가 문서를 관리하기 위해서 선택시 세션에 넣음 */
var addedPost = [];
function doAddPostCart(obj) {
	var srl = obj.value;
	addedPost[addedPost.length] = srl;
	setTimeout(function() { callAddPostCart(addedPost.length); }, 100);
}

function callAddPostCart(document_length) {
	if(addedPost.length<1 || document_length != addedPost.length) return;
	addedPost = Array.from(new Set(addedPost));  // secure uniqueness of array
	var params = [];
	params.post_ids = addedPost.join(",");
console.log(params);
	// exec_xml("document","procDocumentAddCart", params, null);
	x2board_ajax_lock = true;
	jQuery.post(x2board_ajax_info.url, {
				'action': x2board_ajax_info.cmd_post_add_cart,
				'board_id': x2board_ajax_info.board_id,
				'post_ids': addedPost.join(","),
				'security':x2board_ajax_info.nonce}, function(res) {
					x2board_ajax_lock = false;
					if(typeof callback === 'function') {
						callback(res);
					}
					else {
						if(res.result == 'error') {
							alert(res.message);
						}
						// else {
						// 	location.reload(true)
						// }
					}
				});
	addedPost = [];
}

function is_def(v) {
	return (typeof(v)!='undefined');
}

/**
 * @brief 특정 name을 가진 체크박스들의 checked 속성 변경
 * @param [itemName='cart',][options={}]
 */
function checkboxToggleAll(itemName) {
	if(!is_def(itemName)) itemName='cart';
	var obj;
	var options = {
		wrap : null,
		checked : 'toggle',
		doClick : false
	};

	switch(arguments.length) {
		case 1:
			if(typeof(arguments[0]) == "string") {
				itemName = arguments[0];
			} else {
				jQuery.extend(options, arguments[0] || {});
				itemName = 'cart';
			}
			break;
		case 2:
			itemName = arguments[0];
			jQuery.extend(options, arguments[1] || {});
	}

	if(options.doClick === true) options.checked = null;
	if(typeof(options.wrap) == "string") options.wrap ='#'+options.wrap;

	if(options.wrap) {
		obj = jQuery(options.wrap).find('input[name="'+itemName+'"]:checkbox');
	} else {
		obj = jQuery('input[name="'+itemName+'"]:checkbox');
	}

	if(options.checked == 'toggle') {
		obj.each(function() {
			jQuery(this).attr('checked', (jQuery(this).attr('checked')) ? false : true);
		});
	} else {
		if(options.doClick === true) {
			obj.click();
		} else {
			obj.attr('checked', options.checked);
		}
	}
}