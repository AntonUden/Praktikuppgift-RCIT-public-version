var cb;

function base64MimeType(encoded) {
	let result = null;

	if (typeof encoded !== 'string') {
		return result;
	}

	let mime = encoded.match(/data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+).*,.*/);

	if (mime && mime.length) {
		result = mime[1];
	}

	return result;
}

function readURL(input) {
	if (input.files && input.files[0]) {
		let reader = new FileReader();

		reader.onload = function(e) {
			if(!base64MimeType(e.target.result).includes("image/")) {
				$("#btn_crop").prop('disabled', true);
				return;
			}

			$(".image").attr('src', e.target.result);

			if(cb) {
				cb.destroy();
			}
			$('#cropImage').Jcrop({
				onChange: updateCropPreview,
				onSelect: updateCropPreview,
				boxWidth: Math.floor($("#cropDiv").width() - 30),
				boxHeight: Math.floor($("#cropDiv").height() - 60)
			},function(){
				cb = this;
				let nW = $('#cropImage')[0].naturalWidth;
				let nH = $('#cropImage')[0].naturalHeight;
				cb.newSelection();
				cb.setSelect([ 0,0,nW,nH ]);
				cb.refresh();
			});
			$("#btn_crop").prop('disabled', false);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function updateCropPreview(c) {
	if(parseInt(c.w) > 0) {

		let imageObj = $("#cropImage")[0];
		let canvas = $("#image");
		let context = canvas[0].getContext("2d");

		$('#cropX').val(Math.floor(c.x));
		$('#cropY').val(Math.floor(c.y));
		$('#cropW').val(Math.floor(c.w));
		$('#cropH').val(Math.floor(c.h));

		context.canvas.width = c.w;
		context.canvas.height = c.h;

		context.drawImage(imageObj, c.x, c.y, c.w, c.h, 0, 0, canvas[0].width, canvas[0].height);
		if(c.w > 1920 || c.h > 1080) {
			$(".scaledownInfo").show();
		} else {
			$(".scaledownInfo").hide();
		}
		resizeCanvasToDisplaySize(canvas);
	}
}

function resizeCanvasToDisplaySize(canvas) {
	// look up the size the canvas is being displayed
	const width = canvas.clientWidth;
	const height = canvas.clientHeight;

	// If it's resolution does not match change it
	if (canvas.width !== width || canvas.height !== height) {
		canvas.width = width;
		canvas.height = height;
		return true;
	}
	return false;
}

var entityMap = {
	'&': '&amp;',
	'<': '&lt;',
	'>': '&gt;',
	'"': '&quot;',
	"'": '&#39;',
	'/': '&#x2F;',
	'`': '&#x60;',
	'=': '&#x3D;'
};

function escapeHtml (string) {
	return String(string).replace(/[&<>"'`=\/]/g, function (s) {
		return entityMap[s];
	});
}

function nl2br(str) {
	return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
}

function validateInput() {
	if(selectedFile != null) {
		let imgFileExt = selectedFile.split('.').pop();
		if(!(imgFileExt == "png" || imgFileExt == "jpg" || imgFileExt == "jpeg")) {
			return "Ogiltigt filformat. Endast png, jpg och jpeg tillåtet";
		}
	}

	if($('[name="title"]').val().length <= 0) {
		return "Titel saknas";
	}

	if($('[name="category"]').children("option").filter(":selected").text() == "Välj kategori") {
		return "Välj en kategori";
	}

	if($('[name="articleContent"]').val().length <= 0) {
		return "Inget artikel innehåll";
	}

	return "ok";
}

function updatePreview() {
	let title = escapeHtml($('[name="title"]').val());
	let category = $('[name="category"]').children("option").filter(":selected").text();
	let articleContent = nl2br(addTags(escapeHtml($('[name="articleContent"]').val().replace("\\",""))));

	if(selectedFile) {
		$("#image").show();
	} else {
		$("#image").hide();
	}

	$("#title").html(title);
	$("#category").html(category);
	$("#content").html(articleContent);
}

function showPreview() {
	updatePreview();
	$("#preview").show();
	$("#dimScreen").width("0%");
	$("#dimScreen").height("0%");
	$("#mainDiv").hide();
}

var selectedFile = null;
$(document).ready(function() {
	$("#previewButton").click(function() {
		showPreview();
	});

	$("#btn_preview").click(function() {
		showPreview();
	});

	$("#closePreviewButton").click(function() {
		$("#preview").hide();
		$("#dimScreen").width("100%");
		$("#dimScreen").height("100%");
		$("#mainDiv").show();
	});

	$("#closeCropButton").click(function() {
		$("#dimScreen").hide();
		$("#cropDiv").hide();
	});

	$("#btn_send").click(function() {
		$("#confirmBox").hide();
		$("#dimScreen").hide();
		$("#articleForm").submit();
	});

	$("#btn_cancel").click(function() {
		$("#dimScreen").hide();
		$("#confirmBox").hide();
	});

	$("#btn_submit").click(function(){
		let validateInputResult = validateInput();
		if(validateInputResult == "ok") {
			$("#btn_send").prop('disabled',false);
			$("#postError").hide();
		} else {
			$("#btn_send").prop('disabled',true);
			$("#postError").show();
			$("#postError").html(validateInputResult);
		}
		$("#dimScreen").show();
		$("#confirmBox").show();
	});

	$("#btn_crop").click(function(){
		$("#dimScreen").show();
		$("#cropDiv").show();
	});

	$("#articleImage").change(function(e) {
		console.log(e.target.files[0].name);
		selectedFile = e.target.files[0].name;
		readURL(this);
	});
});