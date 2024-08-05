/**
 * @author https://singleview.co.kr
 */

function x2board_kr_zipcode_search(postcode, address_1, address_2, address_3){
	var width = 500;
	var height = 600;
	new daum.Postcode({
		width: width,
		height: height,
		oncomplete: function(data){
			// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
			var addr = ''; // 주소 변수
			var extraAddr = ''; // 참고항목 변수

			//사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
			if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
				addr = data.roadAddress;
			} else { // 사용자가 지번 주소를 선택했을 경우(J)
				addr = data.jibunAddress;
			}

			// 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
			if(data.userSelectedType === 'R'){
				// 법정동명이 있을 경우 추가한다. (법정리는 제외)
				// 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
				if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
					extraAddr += data.bname;
				}
				// 건물명이 있고, 공동주택일 경우 추가한다.
				if(data.buildingName !== '' && data.apartment === 'Y'){
					extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
				}
				// 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
				if(extraAddr !== ''){
					extraAddr = ' (' + extraAddr + ')';
				}
				// 조합된 참고항목을 해당 필드에 넣는다.
				jQuery('#'+address_3).val(extraAddr);
			
			} else {
				jQuery('#'+address_3).val('');
			}

			// 우편번호와 주소 정보를 해당 필드에 넣는다.
			jQuery('#'+postcode).val(data.zonecode);
			jQuery('#'+address_1).val(data.roadAddress);
			setTimeout(function(){
				jQuery('#'+address_2).focus();
			});
		}
	}).open({
		left: (screen.availWidth-width)*0.5,
		top: (screen.availHeight-height)*0.5
	});
}