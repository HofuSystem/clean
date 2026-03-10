const fullToolbar = [
    [
      {
        font: []
      },
      {
        size: []
      }
    ],
    ['bold', 'italic', 'underline', 'strike'],
    [
      {
        color: []
      },
      {
        background: []
      }
    ],
    [
      {
        script: 'super'
      },
      {
        script: 'sub'
      }
    ],
    [
      {
        header: '1'
      },
      {
        header: '2'
      },
      'blockquote',
      'code-block'
    ],
    [
      {
        list: 'ordered'
      },
      {
        list: 'bullet'
      },
      {
        indent: '-1'
      },
      {
        indent: '+1'
      }
    ],
    [{ direction: 'rtl' }],
    ['link', 'image', 'video', 'formula'],
    ['clean']
  ];

  //save
  $(document).on('submit', '#operation-form,.operation-form', function (e) {
    e.preventDefault();

    let form = $(this);
    let data = getFormData($(this));
    let itemsData = getItemsData(form);
    let mainMode = $(this).data('mode');
    if (mainMode == 'new') {
      for (let key in itemsData) {
        data[key] = itemsData[key];
      }
    }
    let url = $(this).attr('action');
    let type = $(this).data('mode') == "edit" ? 'PUT' : 'POST';
    $.ajax({
      type: type,
      url: url,
      data: data,
      dataType: "json",
      beforeSend: function () {
        // Code to run before the request is sent
        $('.loader').addClass('show')
      },
      success: function (response) {
        Swal.fire({
          text: response.message,
          icon: "success",
          buttonsStyling: false,
          confirmButtonText: "Ok",
          customClass: {
            confirmButton: "btn fw-bold btn-success",
          }
        }).then(function () {
          if (mainMode == 'new') {
            window.location.reload();
          }
        })
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Code to run if the request fails
        response = JSON.parse(jqXHR.responseText);
        Swal.fire({
          text: response.message,
          icon: "error",
          buttonsStyling: false,
          confirmButtonText: "Ok",
          customClass: {
            confirmButton: "btn fw-bold btn-success",
          }
        })
        $.each(response.errors, function (key, array) {
          $.each(array, function (index, error) {
            toastr.error(error, key);
          });
        });
      },
      complete: function () {
        $('.loader').removeClass('show')
      }
    });
  });


  function getFormData(form) {
    // Initialize an empty object to store the form data
    var formData = {};

    // Helper function to set nested values in an object
    function setNestedValue(obj, keys, value) {
      keys.reduce((o, key, index) => {
        if (index === keys.length - 1) {
          o[key] = value;
        } else {
          o[key] = o[key] || {};
        }
        return o[key];
      }, obj);
    }

    // Iterate over each form input
    form.find('input, select, textarea').each(function () {
      var input = $(this);
      var name = input.attr('name');
      var value = input.val();

      if (!name) return; // Skip elements without a name attribute

      // Parse the name string for nested keys
      var keys = name
        .replace(/\]/g, '') // Remove closing brackets
        .split('['); // Split by opening brackets

      // Handle checkboxes (multiple values)
      if (input.is(':checkbox')) {
        let multiple = input.parents('.form-group.row').attr('multiple');
        if (multiple === 'multiple') {
          // Initialize the array if it doesn't exist
          let currentValue = keys.reduce((o, key, index) => {
            if (index === keys.length - 1) {
              return o[key] = o[key] || [];
            }
            return o[key] = o[key] || {};
          }, formData);

          // If the checkbox is checked, add its value to the array
          if (input.is(':checked')) {
            currentValue.push(value);
          }
        } else {
          // For single checkboxes, set the value based on whether it's checked
          value = input.is(':checked') ? 1 : 0;
          setNestedValue(formData, keys, value);
        }
      }
      // Handle radio buttons (single value)
      else if (input.is(':radio')) {
        if (input.is(':checked')) {
          setNestedValue(formData, keys, value);
        }
      }
      // Handle other input types (text, select, textarea)
      else {
        setNestedValue(formData, keys, value);
      }
    });

    // Handle divs with custom data
    form.find('div[name]').each(function () {
      let name = $(this).attr('name');
      let keys = name
        .replace(/\]/g, '') // Remove closing brackets
        .split('['); // Split by opening brackets

      setNestedValue(formData, keys, $(this).find('.ql-editor').html());
    });

    return formData;
  }
  function populateForm(form, data) {
    // Helper function to retrieve nested values from the data object
    function getNestedValue(obj, keys) {
      return keys.reduce((o, key) => (o && o[key] !== undefined ? o[key] : undefined), obj);
    }

    // Iterate over each form input
    form.find('input, select, textarea').each(function () {
      var input = $(this);
      var name = input.attr('name');

      if (!name) return; // Skip elements without a name attribute

      // Parse the name string for nested keys
      var keys = name
        .replace(/\]/g, '') // Remove closing brackets
        .split('['); // Split by opening brackets

      // Get the corresponding value from the data object
      var value = getNestedValue(data, keys);

      // Populate the input with the value
      if (input.is(':checkbox')) {
        // Handle checkboxes
        if (Array.isArray(value)) {
          input.prop('checked', value.includes(input.val()));
        } else {
          input.prop('checked', value == 1);
        }
      } else if (input.is(':radio')) {
        // Handle radio buttons
        input.prop('checked', input.val() === value);
      } else {
        // Handle other input types
        input.val(value).trigger('change');
      }
    });

    // Handle divs with custom data
    form.find('div[name]').each(function () {
      let name = $(this).attr('name');
      let keys = name
        .replace(/\]/g, '') // Remove closing brackets
        .split('['); // Split by opening brackets

      let value = getNestedValue(data, keys);

      if (value !== undefined) {
        $(this).find('.ql-editor').html(value);
      }
    });
  }


  $(document).ready(function () {
    drawMediaCenters($('#operation-form'));
    $("table[orderable]").tableDnD({
      onDragClass: "myDragClass",
      onDragStop: function (table, row) {
        reorderTable(table);
      },
    });

    $('.select2').each(function (index, element) {
      let id = $(this).attr('id');
      if ($(this).parents('.modal').length) {
        let parentId = $(this).parents('.modal').first().attr('id');
        $('#' + id).select2({
          allowClear: true,
          dropdownParent: $('#' + parentId)
        });
      } else {

        $('#' + id).select2({
          allowClear: true,
          width: '100%',
          height: '40px',
          placeholder: 'select..'
        });
      }
    });
  });
  function getValueFromPath(data, path) {
    if (!data || !path) return undefined;

    // Split the path into an array of keys
    const keys = path.split('.');

    // Traverse the data object to retrieve the value
    return keys.reduce((current, key) => {
      return current && current[key] !== undefined ? current[key] : undefined;
    }, data);
  }
  function createRaw(display, data, edit_url = "", delete_url = "") {
    let tr = $('<tr>').data('id', data['id']).data('data', data)
    $.each(display, function (indexInArray, valueOfElement) {
      let newTd = "";
      let value = getValueFromPath(data, valueOfElement['name']);
      switch (valueOfElement['type']) {
        case "mediacenter":
          newTd = $('<td>').html(mediaCenterRender(value));
          break;
        case "select":
          newTd = $('<td>').html(selectGetOptionLabel(valueOfElement['name'], value));
          break;
        case "checkbox":
          newTd = $('<td>').html(checkboxRender(valueOfElement['name'], value));
          break;
        case "time":
          newTd = $('<td>').html(timeRender(value));
          break;

        case "actions":
          return;
          break;

        default:
          newTd = $('<td>').html(value);
          break;
      }

      tr.append(newTd);
    });

    let td = $('<td>');
    let div = $('<div>').addClass('d-flex justify-content-center');
    let editButton = $('<button>').addClass('btn-operation edit-item').html('<i class="fas fa-pencil-alt"></i>').data('href', edit_url).addClass('mx-1');
    let deleteButton = $('<button>').addClass('btn-operation delete-item').html(' <i class="fas fa-trash"></i>').data('href', delete_url).addClass('mx-1');

    div.append(editButton);
    div.append(deleteButton);
    td.append(div)
    tr.append(td);
    return tr;
  }

  //items atb
  $(document).on('click', '.items-container .create-new-items', function (e) {
    e.preventDefault();
    let mainMode = $('#operation-form').data('mode');
    let mode = 'create';
    $('.items-container.active').removeClass('active')
    $('.items-container tr.active').removeClass('active')
    $(this).parents('.items-container').addClass('active')
    $(this).parents('tr').addClass('active')
    let clicKedBtn = $(this);
    let modelID = clicKedBtn.parents('.items-container').data("itemsFrom") + 'Modal';
    let R_on = clicKedBtn.parents('.items-container').data("itemsOn")
    let itemsContainer = clicKedBtn.parents('.items-container').first();
    let cols = itemsContainer.find('thead tr th');
    cols.each(function (index, element) {
      let colData = {};
      colData['name'] = $(this).data('name');
      colData['type'] = $(this).data('type');
      cols[index] = colData;
    });

    $('#' + modelID).modal('show')
    $('#' + modelID).data('cols', cols)
    $('#' + modelID).data('R_on', R_on)
    $('#' + modelID).data('mainMode', mainMode)
    $('#' + modelID).data('mode', mode)
    $('#' + modelID + " form").trigger('reset');
    drawMediaCenters($(`#${modelID} form`));
  });

  //edit operations
  $(document).on('click', '.items-container .edit-item', function (e) {
    e.preventDefault();
    let mainMode = $('#operation-form').data('mode');
    let mode = 'edit';

    $('.items-container.active').removeClass('active')
    $('.items-container tr.active').removeClass('active')
    $(this).parents('.items-container').addClass('active')
    $(this).parents('tr').addClass('active')
    let clicKedBtn = $(this);
    let modelID = clicKedBtn.parents('.items-container').data("itemsFrom") + 'Modal';
    let R_on = clicKedBtn.parents('.items-container').data("itemsOn")
    let updateUrl = clicKedBtn.data("href")
    let tr = $(this).parents('tr');
    let data = tr.data('data');
    let itemsContainer = clicKedBtn.parents('.items-container').first();
    let cols = itemsContainer.find('thead tr th');
    cols.each(function (index, element) {
      let colData = {};
      colData['name'] = $(this).data('name');
      colData['type'] = $(this).data('type');
      cols[index] = colData;
    });
    $('#' + modelID).data('cols', cols)
    $('#' + modelID).data('R_on', R_on)
    $('#' + modelID).data('mainMode', mainMode)
    $('#' + modelID).data('mode', mode)
    $('#' + modelID).data('updateUrl', updateUrl)

    populateForm($(`#${modelID} form `), data)
    drawMediaCenters($(`#${modelID} form`));
    $('#' + modelID).modal('show')
  });

  $(document).on('submit', '.items-modal-form', function (e) {
    e.preventDefault();
    $('.current-submit').removeClass('current-submit');
    $(this).addClass('current-submit');
    let modal = $(this).parents('.modal');
    let mainMode = modal.data('mainMode');
    let data = getFormData($(this));
    let form = $(this);
    let mode = modal.data('mode');
    let store = modal.data('store');
    let updateUrl = modal.data('updateUrl');
    let f_key = modal.data('R_on')
    let p_key = $('#operation-form').data('id')
    let display = modal.data('cols')



    data[f_key] = p_key;

    if (mainMode == 'edit') {
      if (mode == 'create') {
        let url = store;
        let type = 'POST';
        $.ajax({
          type: type,
          url: url,
          data: data,
          dataType: "json",
          beforeSend: function () {
            // Code to run before the request is sent
            $('.loader').addClass('show')
          },
          success: function (response) {
            modal.modal('hide');
            Swal.fire({
              text: response.message,
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok",
              customClass: {
                confirmButton: "btn fw-bold btn-success",
              }
            }).then(function (result) {
              tr = createRaw(display, response.entity, response.entity.updateUrl, response.entity.deleteUrl)
              $('.items-container.active table').append(tr)
              $(".items-container.active table[orderable]").tableDnD({
                onDragClass: "myDragClass",
                onDragStop: function (table, row) {
                  reorderTable(table);
                },
              });

            })
          },
          error: function (jqXHR, textStatus, errorThrown) {
            // Code to run if the request fails
            response = JSON.parse(jqXHR.responseText);
            Swal.fire({
              text: response.message,
              icon: "error",
              buttonsStyling: false,
              confirmButtonText: "Ok",
              customClass: {
                confirmButton: "btn fw-bold btn-success",
              }
            })
            $.each(response.errors, function (key, array) {
              $.each(array, function (index, error) {
                toastr.error(error, key);
              });
            });
          },
          complete: function () {
            $('.loader').removeClass('show')
          }
        });
      } else {
        let url = updateUrl;
        let type = 'PUT';
        $.ajax({
          type: type,
          url: url,
          data: data,
          dataType: "json",
          beforeSend: function () {
            // Code to run before the request is sent
            $('.loader').addClass('show')
          },
          success: function (response) {
            modal.modal('hide');
            Swal.fire({
              text: response.message,
              icon: "success",
              buttonsStyling: false,
              confirmButtonText: "Ok",
              customClass: {
                confirmButton: "btn fw-bold btn-success",
              }
            }).then(function (result) {
              tr = createRaw(display, data, response.entity.updateUrl, response.entity.deleteUrl)
              $('.items-container tr.active').replaceWith(tr);
              $(".items-container table[orderable] tr.active").tableDnD({
                onDragClass: "myDragClass",
                onDragStop: function (table, row) {
                  reorderTable(table);
                },
              });
              form.trigger('reset');
            })
          },
          error: function (jqXHR, textStatus, errorThrown) {
            // Code to run if the request fails
            response = JSON.parse(jqXHR.responseText);
            Swal.fire({
              text: response.message,
              icon: "error",
              buttonsStyling: false,
              confirmButtonText: "Ok",
              customClass: {
                confirmButton: "btn fw-bold btn-success",
              }
            })
            $.each(response.errors, function (key, array) {
              $.each(array, function (index, error) {
                toastr.error(error, key);
              });
            });
          },
          complete: function () {
            $('.loader').removeClass('show')
          }
        });
      }
    } else {
      if (mode == 'create') {
        tr = createRaw(display, data)
        $('.items-container.active table').append(tr)
        $(".items-container.active table[orderable]").tableDnD({
          onDragClass: "myDragClass",
          onDragStop: function (table, row) {
            reorderTable(table);
          },
        });
        form.trigger('reset');
        modal.modal('hide');
      } else {
        //applay edit
        tr = createRaw(display, data)
        $('.items-container tr.active').replaceWith(tr);
        $(".items-container.active table[orderable]").tableDnD({
          onDragClass: "myDragClass",
          onDragStop: function (table, row) {
            reorderTable(table);
          },
        });
        form.trigger('reset');
        modal.modal('hide');
      }

    }

    $('#itemsModal').modal('hide')

  });


  function getItemsData(form) {
    let itemsData = {};
    form.find('.items-container').each(function (params) {

      let orderKey = $(this).find('table').data('orderKey');
      if (orderKey) {
        let orderable = $(this).find('table').attr('orderable').length;
      }

      let name = $(this).data('itemsName');
      let order = 1;
      let rowsData = [];

      $(this).find('tbody tr').each(function (params) {
        row = $(this)
        rowData = $(this).data('data')
        console.log(name, rowData);
        if (orderKey) {
          rowData[orderKey] = order;
          order = order + 1;
        }
        rowsData.push(rowData);
      })
      itemsData[name] = rowsData;
    })
    return itemsData;
  }


  $(document).on('click', '.delete-item', function (e) {
    e.preventDefault();

    $('.items-container.active').removeClass('active')
    $('.items-container tr.active').removeClass('active')
    $(this).parents('.items-container').addClass('active')
    $(this).parents('tr').addClass('active')
    let clicKedBtn = $(this);
    let modelID = clicKedBtn.parents('.items-container').data("itemsFrom") + 'DeleteModel';
    let href = $(this).data('href');
    $("#" + modelID + " .items-final-delete").data('url', href)
    $("#" + modelID + " .items-final-delete").data('modelID', modelID)
    $("#" + modelID).modal('show')
  });

  $('.items-final-delete').click(function (e) {
    e.preventDefault();
    let url = $(this).data('url');
    let modelID = $(this).data('modelID');

    if (url.length) {
      $.ajax({
        type: "DELETE",
        url: url,
        data: {},
        dataType: "json",
        success: function (response) {
          $('#' + modelID).modal('hide');
          $('.items-container tr.active').remove();

        }
      });
    } else {
      $('#' + modelID).modal('hide');
      $('.items-container tr.active').remove();
    }
  });
  function reorderTable(table) {
    table = $(table);
    let trs = table.find('tbody tr');
    let list = [];
    $.each(trs, function (indexInArray, valueOfElement) {
      let item = {};
      id = $(valueOfElement).data('id');
      item.id = id;
      item.order = (indexInArray + 1);
      list.push(item)
    });
    let mainMode = $('#operation-form').data('mode');
    if (mainMode == 'edit') {
      let orderUrl = table.data('orderUrl');
      let data = {};
      data['list'] = list;
      $.ajax({
        type: "POST",
        url: orderUrl,
        data: data,
        dataType: "json",
        success: function (response) {
          Swal.fire({
            text: response.message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok",
            customClass: {
              confirmButton: "btn fw-bold btn-success",
            }
          })
        }
      });
    } else {

    }
  }
  //render
  function selectGetOptionLabel(name, value) {
    if (!value) {
      return "";
    }
    let select = $(`.current-submit select[name="${name}"]`);
    let options = select.find('option');
    let label = "";
    options.each(function (index, element) {
      if ($(this).val() == value) {
        label = $(this).text();
      }
    });

    return label;
  }
  function mediaCenterRender(value) {
    let images = "";
    let url = media_center_links.url;

    if (value && value.includes(',')) {
      files = value.split(',');
      files.forEach(file => {
        if (!file.startsWith(url)) {
          file = url + '/' + file;
        }
        images += '<a href="' + file + '" target="_blank"><img src="' + file + '"></a>';
      });
      return images;
    }
    if (value && value.length) {
      if (!value.startsWith(url)) {
        file = url + '/' + value;
      }
      images = '<a href="' + file + '" target="_blank"><img src="' + file + '"></a>';

    }
    let div = $('<div>');
    div.append(images);
    return div;
  }
  function checkboxRender(name, value) {
    return (value)
      ? `<span class="p-1 rounded bg-success text-white">yes</span>`
      : `<span class="p-1 rounded bg-danger text-white">no</span>`;

  }
  function timeRender(time) {
    if (time) {
      let [hours, minutes] = time.split(":");
      let period = hours >= 12 ? "PM" : "AM";
      hours = hours % 12 || 12; // Convert 24-hour format to 12-hour format

      return `${hours}:${minutes} ${period}`;

    }
  }
  $(document).ready(function () {
    $('.advance-select').each(function (index, element) {
      let name = $(this).attr('name') + "...";
      if ($(this).parents('.modal').length) {
        let id = $(this).parents('.modal').first().attr('id');
        $(this).select2({
          allowClear: true,
          placeholder: name,
          dropdownParent: $('#' + id),
          placeholder: "Select an option",
          width: '100%' // Force width


        }).trigger('change');
      } else {
        $(this).select2({
          allowClear: true,
          placeholder: name,
          placeholder: "Select an option",
          width: '100%' // Force width


        }).trigger('change');
      }
    });

    // Loop through all elements with the class 'editor-container'
    document.querySelectorAll('.editor-container').forEach(function (container) {
      // Find the first inner div inside the container
      var editorElement = container.querySelector('div');


      // Initialize Quill for the editor element
      if (editorElement) {
        var quill = new Quill(editorElement, {
          theme: 'snow',
          modules: {
            toolbar: fullToolbar
          }
        });

        // Custom image handler
        var toolbar = quill.getModule('toolbar');
        toolbar.addHandler('image', function() {
          // Check if media center links are available
          if (typeof media_center_links === 'undefined' || !media_center_links.add_new) {
            alert('Media center is not properly configured. Please refresh the page and try again.');
            return;
          }

          var input = document.createElement('input');
          input.setAttribute('type', 'file');
          input.setAttribute('accept', 'image/*');
          input.click();

          input.onchange = function() {
            var file = input.files[0];
            if (file) {
              // Check file size (limit to 5MB)
              if (file.size > 5 * 1024 * 1024) {
                alert('Image file size must be less than 5MB.');
                return;
              }

              var formData = new FormData();
              formData.append('files[]', file);
              formData.append('max', 1);
              formData.append('type', 'image');
              formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

              // Show loading message
              var range = quill.getSelection();
              quill.insertText(range.index, 'Uploading image...', 'italic');
              var loadingIndex = range.index;

              $.ajax({
                url: media_center_links.add_new,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                  if (response.data && response.data.length > 0) {
                    // Remove loading text
                    quill.deleteText(loadingIndex, 'Uploading image...'.length);

                    var imageUrl = media_center_links.url + '/' + response.data[0].file_name;
                    quill.insertEmbed(loadingIndex, 'image', imageUrl);
                  }
                },
                error: function(xhr, status, error) {
                  // Remove loading text
                  quill.deleteText(loadingIndex, 'Uploading image...'.length);

                  console.error('Error uploading image:', error);
                  // Show error message to user
                  if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert('Error uploading image: ' + xhr.responseJSON.message);
                  } else {
                    alert('Error uploading image. Please try again.');
                  }
                }
              });
            }
          };
        });
      }
    });

  });
  $(document).ready(function () {
    $('.map-container').each(function () {
      var container = $(this);
      var mapElement = container.find('.map')[0];
      var latInput = container.find('.lat');
      var lngInput = container.find('.lng');
      var searchBox = container.find('.search-box');
      var suggestionsBox = container.find('.suggestions');
      var lat = latInput.val();
      if (!lat) {
        lat = 25.624262;
      }
      var lng = lngInput.val();
      if (!lng) {
        lng = 42.352833
      }
      console.log(latInput.val());


      var map = L.map(mapElement).setView([lat, lng], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
      setInterval(function () {

        map.invalidateSize();
      }, 2500);
      var marker = L.marker([lat, lng], { draggable: true }).addTo(map)
        .bindPopup('Drag me!')
        .openPopup();

      latInput.val(lat);
      lngInput.val(lng);

      marker.on('dragend', function () {
        var position = marker.getLatLng();
        latInput.val(position.lat.toFixed(6));
        lngInput.val(position.lng.toFixed(6));
        map.setView(position, 13);
      });

      let lastQueryLength = 0;
      searchBox.on('input', function () {
        var query = $(this).val();
        if (Math.abs(query.length - lastQueryLength) <= 2) {
          return;
        }
        lastQueryLength = query.length;
        if (query.length < 3) {
          suggestionsBox.hide();
          return;
        }
        $.getJSON(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`, function (data) {
          suggestionsBox.empty().show();
          data.forEach(function (item) {
            suggestionsBox.append(`<div data-lat="${item.lat}" data-lng="${item.lon}">${item.display_name}</div>`);
          });
        });
      });

      suggestionsBox.on('click', 'div', function () {
        var lat = parseFloat($(this).attr('data-lat'));
        var lng = parseFloat($(this).attr('data-lng'));
        var locationName = $(this).text();
        latInput.val(lat.toFixed(6));
        lngInput.val(lng.toFixed(6));
        map.setView([lat, lng], 13);
        marker.setLatLng([lat, lng]).bindPopup(locationName).openPopup();
        searchBox.val(locationName);
        suggestionsBox.hide();
      });

      latInput.add(lngInput).on('change', function () {
        var lat = parseFloat(latInput.val());
        var lng = parseFloat(lngInput.val());
        if (!isNaN(lat) && !isNaN(lng)) {
          map.setView([lat, lng], 13);
          marker.setLatLng([lat, lng]).bindPopup('Updated Position').openPopup();
        }
      });

      $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-box, .suggestions').length) {
          suggestionsBox.hide();
        }
      });
    });
  });
