let uploadButton = document.getElementById("upload-button");
let chosenImage = document.getElementById("chosen-image");
let fileName = document.getElementById("file-name");
let container = document.querySelector(".image-container");
let error = document.getElementById("error");
let imageDisplay = document.getElementById("image-display");
let page = 1;
let mediaChooseBtn = null;
const fileHandler = (file, name, type) => {
  if (type.split("/")[0] !== "image") {
    //File Type Error
    error.innerText = "Please upload an image file";
    return false;
  }
  error.innerText = "";
  let reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onloadend = () => {
    //image and file name
    let imageContainer = document.createElement("figure");
    let img = document.createElement("img");
    let progress = document.createElement("div");
    let progress_bar = document.createElement("div");

    progress.setAttribute('class', 'progress')
    progress_bar.setAttribute('class', 'progress-bar ')
    progress_bar.setAttribute('role', 'progressbar')

    img.src = reader.result;


    progress.appendChild(progress_bar);
    imageContainer.appendChild(img);
    imageContainer.appendChild(progress);
    imageContainer.innerHTML += `<figcaption>${name}</figcaption>`;
    imageDisplay.appendChild(imageContainer);
  };
};
if (uploadButton) {
  //Upload Button
  uploadButton.addEventListener("change", () => {
    imageDisplay.innerHTML = "";
    var formData = new FormData();
    Array.from(uploadButton.files).forEach((file) => {
      fileHandler(file, file.name, file.type);
      formData.append('files[]', file);
    });
    formData.append('max', $('#mediaModal').data('max'))
    formData.append('type', $('#mediaModal').data('type'))
    $.ajax({
      url: media_center_links.add_new,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      xhr: function () {
        var xhr = new XMLHttpRequest();
        xhr.upload.onprogress = function (event) {
          if (event.lengthComputable) {
            var percentComplete = (event.loaded / event.total) * 100;
            $('.progress-bar').css('width', percentComplete + '%')
          }
        };
        return xhr;
      },
      success: function (response) {
        $.each(response.data, function (indexInArray, media) {
          $('.media-gallery .row').prepend(createCard(media.file_name));
        });
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error('Error uploading file: ' + textStatus + ' - ' + errorThrown);
      },
      complete: function (xhr, status) {

        $('#image-display figure').hide('slow', function () {
          $(this).remove();
        });
      }
    });
  });
}

if (container) {
  container.addEventListener(
    "dragenter",
    (e) => {
      e.preventDefault();
      e.stopPropagation();
      container.classList.add("active");
    },
    false
  );

  container.addEventListener(
    "dragleave",
    (e) => {
      e.preventDefault();
      e.stopPropagation();
      container.classList.remove("active");
    },
    false
  );

  container.addEventListener(
    "dragover",
    (e) => {
      e.preventDefault();
      e.stopPropagation();
      container.classList.add("active");
    },
    false
  );

  container.addEventListener(
    "drop",
    (e) => {
      e.preventDefault();
      e.stopPropagation();
      container.classList.remove("active");
      let draggedData = e.dataTransfer;
      let files = draggedData.files;
      imageDisplay.innerHTML = "";
      Array.from(files).forEach((file) => {
        fileHandler(file, file.name, file.type);
      });
    },
    false
  );
}


window.onload = () => {
  if (error) {
    error.innerText = "";

  }
};
function loadISelectedMediaCenter() {
  $('.selected-gallery .row').html('');
  let gallery = mediaChooseBtn.parent().find('.input-gallery').first()
  gallery.find('img').each(function (index, element) {
    let value = $(this).data('value');
    console.log(value);
    
    $('.selected-gallery .row').prepend(createCard(value));
  });
}
function loadIMediaCenter() {
  loadISelectedMediaCenter()
  $('.media-gallery .row').html('');
  data = {};
  data['type'] = $('#mediaModal').data('type');
  data['page'] = page;
  $.ajax({
    type: "GET",
    url: media_center_links.list,
    data: data,
    dataType: "json",
    success: function (response) {
      $.each(response.data, function (indexInArray, media) {
        if(!inSelected(media.file_name)){
          $('.media-gallery .row').append(createCard( media.file_name));
        }
      });
    }
  });
}
function inSelected(id) {
  let founded = false;
  $('.selected-gallery .select-item').each(function (index, element) {
    let selectedId = $(this).data('value');        
    if(selectedId == id){
      founded =  true;
    }    
  });
  return founded;
}
function loadMoreIMediaCenter() {
  page += 1
  data = {};
  data['type'] = $('#mediaModal').data('type');
  data['page'] = page;
  $.ajax({
    type: "GET",
    url: media_center_links.list,
    data: data,
    dataType: "json",
    success: function (response) {
      $.each(response.data, function (indexInArray, media) {
        if(!inSelected(media.file_name)){
          $('.media-gallery .row').append(createCard(media.file_name));
        }

      });
    }
  });
}
function getSrcFromValue(value){
  let url = media_center_links.url;
  return url+'/'+value;
}

function createCard(value) {  
  title = shortenString(value);
  label = shortenString(value);
  // Create the card container
  var cardContainer = document.createElement('div');
  cardContainer.className = 'col-lg-3 col-md-4 col-sm-6';

  // Create the card element
  var card = document.createElement('div');
  card.className = 'card media-center-card';

  // Create delete
  var deleteButton = document.createElement('button');
  deleteButton.className = 'brn btn-danger delete-btn';

  var deleteIcon = document.createElement('i');
  deleteIcon.className = 'text-white  fas fa-trash-alt';
  deleteButton.appendChild(deleteIcon);
  card.appendChild(deleteButton);

  // Create the card body
  var cardBody = document.createElement('div');
  cardBody.className = 'card-body select-item';
  cardBody.setAttribute('data-value', value);

  // Create the card image
  var image = document.createElement('img');
  image.src = getSrcFromValue(value);
  image.className = 'card-img-top';
  image.alt = title;
  cardBody.appendChild(image);



  // Create the card title
  var cardTitle = document.createElement('h5');
  cardTitle.className = 'card-title';
  cardTitle.textContent = title;
  cardBody.appendChild(cardTitle);

  

  card.appendChild(cardBody);
  cardContainer.appendChild(card);

  return cardContainer;
}
function shortenString(str) {
  if (str.length > 15) {
    return str.substr(0, 15) + '...';
  } else {
    return str;
  }
}

$(document).on('click', '.media-gallery .delete-btn', function (e) {
  e.preventDefault();
  let deleteBtn = $(this);
  let file_name = deleteBtn.parent().find('.card-body').data('value');
  data = {};
  data['file_name'] = file_name;
  $.ajax({
    type: "Delete",
    url: media_center_links.delete,
    data: data,
    dataType: "json",
    success: function (response) {
      deleteBtn.parent().parent().hide('slow', function () {
        $(this).remove();
      });
    }
  });
});

$('#load-more-media').click(function (e) {
  loadMoreIMediaCenter()
})
$('.media-center-load').click(function (e) {
  e.preventDefault();
  mediaChooseBtn  = $(this) 
  let max         = $(this).parents('.media-center-group').data('max');
  let type        = $(this).parents('.media-center-group').data('type');
  $('#mediaModal').data('type', type)
  $('#mediaModal').data('max', max)
  loadIMediaCenter();
  $('#mediaModal').modal('toggle');
});

$('#mediaUploadBtn').click(function (e) {
  e.preventDefault();
  let gallery = mediaChooseBtn.parent().find('.input-gallery');
  let input   = mediaChooseBtn.parent().find('input');  
  let data    = [];
  let row     = $('<div>').addClass('row');
  $('.selected-gallery .card-body').each(function (index, element) {
    let value = $(this).data('value');        
    row.append(createInputGalleryImage(value))
    if ($('#mediaModal').data('max') == 1) {
      data = value
    } else {
      data.push(value)
    }

  });
  gallery.html(row);
  input.val(data).trigger('change');
  $('#mediaModal').modal('toggle');
});


$(document).on('click', '.select-item', function (e) {
  let max     = $('#mediaModal').data('max');
  let current = $('.selected-gallery .row .media-center-card').length;
  error.innerText = ""

  if (current == max) {
    error.innerText = "Max limit is reached"
    return
  }
  let value = $(this).data('value');
  $('.selected-gallery .row').prepend(createCard(value));
  $(this).parent().parent().remove()
});
$(document).on('click', '.selected-gallery .delete-btn', function (e) {
  e.preventDefault();
  error.innerText = ""
  let deleteBtn = $(this);
  let cardBody  = deleteBtn.parent().find('.card-body');
  let value = cardBody.data('value');
  $('.media-gallery .row').prepend(createCard(value));
  cardBody.parent().parent().remove()


});
function drawMediaCenters(form) {
  $(form.find('.media-center-group input')).each(function (index, element) {
    let input   = $(this);
    let gallery = $(this).parent().find('.input-gallery');    
    gallery.html("");
    let val     = input.val();
    row = $('<div>').addClass('row');
    gallery.append(row)
    gallery.find('.row').append(createInputGalleryImage(val));
  });
}
function createInputGalleryImage(media) {
  let url     = media_center_links.url;
  if(media  && media.includes(',')){
    files = media.split(',');
    let images = "";
    files.forEach(file => {
      let fileUrl = file;
      if(!file.startsWith(url)){
        fileUrl = getSrcFromValue(file);
      }
      images += '<img src="'+fileUrl+'" data-value="'+file+'">'
    });
    return images;
  }
  if(media && media.length){
    if(!media.startsWith(url)){
      url  = getSrcFromValue(media);
    }
    const img = $('<img>').prop('src',url).data('value',media);
    return img;
  }else{
    return null;
  }
 
}