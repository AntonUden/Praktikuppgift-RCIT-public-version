var cb;

function readURL(input) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();

		reader.onload = function(e) {
			$("#cropImage").attr('src', e.target.result);
			startJcrop();
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function getCanvasImage() {
	let canvas = $("#profile_picture_canvas");
	return canvas[0].toDataURL();
}

function startJcrop() {
	if(cb) {
		cb.destroy();
	}
	$('#cropImage').Jcrop({
		aspectRatio: 1,
		onChange: updateCrop,
		onSelect: updateCrop,
		boxWidth: Math.floor(window.innerWidth / 2),
		boxHeight: Math.floor(window.innerHeight / 2)
	},function(){
		cb = this;
		let nW = $('#cropImage')[0].naturalWidth;
		let nH = $('#cropImage')[0].naturalHeight;
		cb.newSelection();
		cb.setSelect([ 0,0,nW,nH ]);
		cb.refresh();
	});
}

function updateCrop(c) {
	if(parseInt(c.w) > 0) {

		let imageObj = $("#cropImage")[0];
		let canvas = $("#profile_picture_canvas");
		let context = canvas[0].getContext("2d");

		context.canvas.width = c.w;
		context.canvas.height = c.h;
		
		context.drawImage(imageObj, c.x, c.y, c.w, c.h, 0, 0, canvas[0].width, canvas[0].height);
	}
}

$(document).ready(function() {
	$("#profile_picture").change(function(e) {
		let ext = $('#profile_picture').val().split('.').pop();
		if(ext == "png" || ext == "jpeg" || ext == "jpg") {
			$("#btn_delete").attr('disabled', false);
			$("#btn_set").attr('disabled', false);
			readURL(this);
		}
	});

	$("#btn_delete").click(function() {
		$.ajax({ type: "POST", url:"", data:{action:"delete"}}).done(function(response) {
			location.reload();
		});
	});

	$("#btn_set").click(function(response) {
		$.ajax({ type: "POST", url:"", data:{action:"set", imgBase64: getCanvasImage()}}).done(function(response) {
			console.log(response);
			location.reload();
		});
	});
	
	if($("#cropImage").attr('src') == "/img/user-icon.png") {
		$("#btn_delete").attr('disabled', true);
		$("#btn_set").attr('disabled', true);
	} else {
		startJcrop();
	}
});