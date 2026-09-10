let uploadButton = document.getElementById("upload-button");
let chosenImage = document.getElementById("chosen-image");
let fileName = document.getElementById("file-name");
let container = document.querySelector(".image-container");
let error = document.getElementById("error");
let imageDisplay = document.getElementById("image-display");
let page = 1;
let mediaChooseBtn = null;
let mediaLoading = false;
let mediaUploading = false;
const mediaText = (ar, en) => $('html').attr('lang') === 'ar' ? ar : en;
function mediaIcon(name) {
  const paths = {
    trash: '<path d="M3 6h18M9 6V4h6v2M5 6l1 14h12l1-14M10 10v6m4-6v6"/>',
    close: '<path d="m6 6 12 12M6 18 18 6"/>',
    check: '<path d="m5 12 4 4L19 6"/>'
  };
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + paths[name] + '</svg>';
}
function updateMediaSelection() {
  const selected = $('#mediaModal .selected-gallery .media-center-card').length;
  const max = Number($('#mediaModal').data('max'));
  $('#media-selected-count').text(selected);
  $('#media-selected-empty').prop('hidden', selected > 0);
  $('#media-library-empty').prop('hidden', mediaLoading || $('.media-gallery .media-center-card').length > 0);
  $('.media-footer-count').text(mediaText('تم اختيار ', 'Selected: ') + selected + (max > 0 ? ' / ' + max : ''));
  $('#mediaModal .media-center-card').each(function () {
    const isSelected = $(this).closest('.selected-gallery').length > 0;
    const action = isSelected ? mediaText('إلغاء الاختيار', 'Deselect') : mediaText('اختيار', 'Select');
    $(this).find('.select-item').attr('aria-pressed', String(isSelected)).attr('aria-label', action + ': ' + $(this).find('.card-title').text());
    const deleteLabel = isSelected ? mediaText('إلغاء الاختيار', 'Deselect') : mediaText('حذف من المكتبة', 'Delete from library');
    const deleteBtn = $(this).find('.delete-btn');
    deleteBtn.attr('aria-label', deleteLabel).attr('title', deleteLabel);
    const icon = isSelected ? 'close' : 'trash';
    if (deleteBtn.data('icon') !== icon) deleteBtn.data('icon', icon).html(mediaIcon(icon));
  });
}
function uploadMediaFiles(files) {
  if (mediaUploading) return;
  error.innerText = '';
  const isFile = $('#mediaModal').data('type') === 'file';
  const extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
  const accepted = Array.from(files).filter(file => isFile
    ? extensions.includes(file.name.split('.').pop().toLowerCase())
    : file.type.split('/')[0] === 'image');
  if (accepted.length !== files.length) error.innerText = mediaText('بعض الملفات غير مدعومة. اختر صورًا أو ملفات من الأنواع المسموحة.', 'Some files are unsupported. Choose images or an allowed file type.');
  if (!accepted.length) return;
  const formData = new FormData();
  imageDisplay.innerHTML = '';
  const previews = [];
  accepted.forEach(file => {
    formData.append('files[]', file);
    const figure = document.createElement('figure');
    const img = document.createElement('img');
    img.alt = file.name;
    if (file.type.startsWith('image/')) {
      img.src = URL.createObjectURL(file);
      previews.push(img.src);
    } else img.src = getSrcFromValue(file.name);
    const caption = document.createElement('figcaption');
    caption.textContent = file.name;
    const progress = document.createElement('div');
    progress.className = 'progress';
    const bar = document.createElement('div');
    bar.className = 'progress-bar';
    progress.appendChild(bar);
    figure.append(img, progress, caption);
    imageDisplay.appendChild(figure);
  });
  formData.append('max', $('#mediaModal').data('max'));
  formData.append('type', $('#mediaModal').data('type'));
  mediaUploading = true;
  $('#upload-button, #mediaUploadBtn, #mediaModal [data-bs-dismiss]').prop('disabled', true);
  $('#mediaModal .image-container').attr('aria-busy', 'true');
  $('#media-status').text(mediaText('جارٍ رفع الملفات…', 'Uploading files…'));
  $.ajax({
    url: media_center_links.add_new, type: 'POST', data: formData, processData: false, contentType: false,
    xhr: function () {
      const xhr = new XMLHttpRequest();
      xhr.upload.onprogress = event => {
        if (event.lengthComputable) $('#mediaModal .progress-bar').css('width', (event.loaded / event.total * 100) + '%');
      };
      return xhr;
    },
    success: function (response) {
      const max = Number($('#mediaModal').data('max'));
      const uploaded = Object.values(response.data || {}).filter(media => media && media.file_name);
      if (max === 1 && uploaded.length) $('.media-gallery .row').prepend($('.selected-gallery .row').children());
      let overflow = false;
      uploaded.forEach(media => {
        if (inSelected(media.file_name)) return;
        const current = $('.selected-gallery .media-center-card').length;
        const select = !max || current < max;
        if (!select) overflow = true;
        $(select ? '.selected-gallery .row' : '.media-gallery .row').prepend(createCard(media.file_name));
      });
      updateMediaSelection();
      if (uploaded.length < accepted.length) error.innerText = mediaText('لم يتم رفع بعض الملفات. يمكنك المحاولة مرة أخرى.', 'Some files could not be uploaded. Please try again.');
      $('#media-status').text(overflow
        ? mediaText('اكتمل الرفع. بقيت الملفات الزائدة في المكتبة لأنك وصلت لحد الاختيار.', 'Upload complete. Extra files remain in the library because the selection limit was reached.')
        : uploaded.length ? mediaText('اكتمل الرفع وتم اختيار الوسائط تلقائيًا.', 'Upload complete. Media selected automatically.') : '');
    },
    error: function (xhr) {
      error.innerText = (xhr.status === 422 && typeof xhr.responseJSON?.message === 'string')
        ? xhr.responseJSON.message
        : mediaText('تعذّر رفع الملفات. حاول مرة أخرى.', 'Upload failed. Please try again.');
      $('#media-status').text('');
    },
    complete: function () {
      mediaUploading = false;
      $('#upload-button, #mediaUploadBtn, #mediaModal [data-bs-dismiss]').prop('disabled', false);
      $('#mediaModal .image-container').attr('aria-busy', 'false');
      imageDisplay.innerHTML = '';
      uploadButton.value = '';
      previews.forEach(url => URL.revokeObjectURL(url));
    }
  });
}
if (uploadButton) uploadButton.addEventListener('change', () => uploadMediaFiles(uploadButton.files));
if (container) {
  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    container.addEventListener(eventName, event => {
      event.preventDefault();
      event.stopPropagation();
      container.classList.toggle('active', eventName === 'dragenter' || eventName === 'dragover');
      if (eventName === 'drop') uploadMediaFiles(event.dataTransfer.files);
    });
  });
}
$('#mediaModal').on('hide.bs.modal', function (event) { if (mediaUploading || mediaLoading) event.preventDefault(); });

function loadISelectedMediaCenter() {
  $('.selected-gallery .row').html('');
  let gallery = mediaChooseBtn.parent().find('.input-gallery').first()
  gallery.find('img').each(function (index, element) {
    let value = $(this).data('value');

    
    $('.selected-gallery .row').prepend(createCard(value));
  });
}
function fetchMediaPage(nextPage) {
  if (mediaLoading) return;
  mediaLoading = true;
  $('#load-more-media').prop('disabled', true).prop('hidden', false);
  $('#media-library-empty').prop('hidden', true);
  $('#media-status').text(mediaText('جارٍ تحميل المكتبة…', 'Loading library…'));
  $.ajax({
    type: 'GET', url: media_center_links.list,
    data: {type: $('#mediaModal').data('type'), page: nextPage}, dataType: 'json',
    success: function (response) {
      const mediaItems = Object.values(response.data || {});
      mediaItems.forEach(media => {
        const exists = $('.media-gallery .select-item').toArray().some(item => $(item).data('value') === media.file_name);
        if (!inSelected(media.file_name) && !exists) $('.media-gallery .row').append(createCard(media.file_name));
      });
      page = nextPage;
      $('#load-more-media').prop('hidden', mediaItems.length === 0);
      $('#media-status').text('');
    },
    error: function () {
      $('#media-status').text('');
      error.innerText = mediaText('تعذّر تحميل المكتبة. اضغط تحميل المزيد للمحاولة مرة أخرى.', 'Could not load the library. Click load more to try again.');
    },
    complete: function () {
      mediaLoading = false;
      $('#load-more-media').prop('disabled', false);
      updateMediaSelection();
    }
  });
}
function loadIMediaCenter() {
  page = 0;
  error.innerText = '';
  $('#media-status').text('');
  loadISelectedMediaCenter();
  $('.media-gallery .row').empty();
  updateMediaSelection();
  fetchMediaPage(1);
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
  error.innerText = '';
  fetchMediaPage(page + 1);
}
function getSrcFromValue(value){
  let url = media_center_links.url;
  let ext = value.split('.').pop().toLowerCase();
  let imageExtensions = ['gif', 'webp', 'jpg', 'jpeg', 'png', 'svg'];
  if (!imageExtensions.includes(ext)) {
    return '/control/icons/' + ext + '.png';
  }
  return url+'/'+value;
}

function createCard(value) {
  const name = value.split('/').pop();
  const wrapper = document.createElement('div');
  const card = document.createElement('div');
  card.className = 'card media-center-card';
  const remove = document.createElement('button');
  remove.type = 'button';
  remove.className = 'delete-btn';
  remove.innerHTML = mediaIcon('trash');
  remove.setAttribute('aria-label', mediaText('حذف من المكتبة', 'Delete from library'));
  const body = document.createElement('button');
  body.type = 'button';
  body.className = 'card-body select-item';
  body.setAttribute('data-value', value);
  body.setAttribute('aria-pressed', 'false');
  body.setAttribute('aria-label', mediaText('اختيار: ', 'Select: ') + name);
  body.title = name;
  const image = document.createElement('img');
  image.src = getSrcFromValue(value);
  image.alt = '';
  image.loading = 'lazy';
  const title = document.createElement('span');
  title.className = 'card-title';
  title.textContent = name;
  const mark = document.createElement('span');
  mark.className = 'media-selection-mark';
  mark.innerHTML = mediaIcon('check');
  body.append(image, title, mark);
  card.append(remove, body);
  wrapper.appendChild(card);
  return wrapper;
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
  let data = {file_name: file_name};
  $.ajax({
    type: "Delete",
    url: media_center_links.delete,
    data: data,
    dataType: "json",
    error: function () {
      error.innerText = mediaText('تعذّر حذف الملف. حاول مرة أخرى.', 'Could not delete the file. Please try again.');
    },
    success: function (response) {
      deleteBtn.parent().parent().remove();
      updateMediaSelection();
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

  if (type === 'file') {
    $('#upload-button').attr('accept', '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.gif');
    let labelText = $('#mediaModal').data('trans-files') || 'Choose Or Drop Files';
    $('#upload-lable').text(labelText);
  } else {
    $('#upload-button').attr('accept', '.jpg,.jpeg,.png,.webp,.gif');
    let labelText = $('#mediaModal').data('trans-photos') || 'Choose Or Drop Photos';
    $('#upload-lable').text(labelText);
  }

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


$(document).on('click', '#mediaModal .select-item, #mediaModal .selected-gallery .delete-btn', function (event) {
  event.preventDefault();
  const card = $(this).closest('.media-center-card').parent();
  error.innerText = '';
  if (card.closest('.selected-gallery').length) {
    $('.media-gallery .row').prepend(card);
  } else {
    const max = Number($('#mediaModal').data('max'));
    const current = $('.selected-gallery .media-center-card').length;
    if (max === 1) $('.media-gallery .row').prepend($('.selected-gallery .row').children());
    else if (max > 0 && current >= max) {
      error.innerText = mediaText('تم الوصول للحد الأقصى المسموح به من الوسائط', 'Max limit is reached');
      return;
    }
    $('.selected-gallery .row').prepend(card);
  }
  updateMediaSelection();
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