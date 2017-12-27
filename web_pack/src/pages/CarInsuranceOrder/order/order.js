import '@/assets/css/api/carInsurance/order.css'

document.addEventListener("DOMContentLoaded", ()=>{
	let eye = document.querySelector('#eye');
	if (eye) {
		eye.addEventListener('click', (e)=>{//眼睛的睁闭
			e.stopPropagation();

			let img = eye.querySelector('img');
			if (img.src.match(/(closeEye)/)) {
				img.src = img.getAttribute('data-on');
				$(".pay1").hide();
				$(".pay2").removeClass('d-n');
				$('#eye img').attr('data-status', 'on')
			}else {
				img.src = img.getAttribute('data-off');
				$(".pay2").addClass('d-n');
				$(".pay1").show();
				$('#eye img').attr('data-status', 'off')
			}
		})
	}
}, false);
