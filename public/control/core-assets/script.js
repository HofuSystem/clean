//system builder
let fields = [];
let dragData = {};
let eIndex = 0;
let mediaChooesdBtn = null;

let selectables = ['select', 'switches', 'checkbox', 'radioButton'];
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


// Drag and drop functions
function allowDrop(ev) {
  ev.preventDefault();
  $(".form-builder").addClass("dragging");
}

function drag(ev) {
  dragData['type'] = ev.target.getAttribute("data-type");
}

function drop(ev) {
  ev.preventDefault();
  $(".form-builder").removeClass("dragging");
  var elementType = dragData['type'];
  //now let' do it
  json = createElementJson(elementType, elementType, elementType, "", [], 'col-md-12');
  updateFormJSON('add', fields.length, json);
  form = {};
  form.fields = fields;
  form.submitBtnText = 'submit';
  buildForm($('#builder'), form, true)
}

function createElementJson(label, name, type, defaultValue, options, bootstrapSize) {

  return {
    "label": label,
    "name": name,
    "type": type,
    "size": bootstrapSize,
    "defaultValue": defaultValue,
    "options": options
  };
}
// Update form JSON
function updateFormJSON(action, index = 0, element) {
  if (action == 'add') {
    fields.splice(index, 0, element);
  } else if (action == 'update') {
    fields[index] = element;
  } else if (action == 'delete') {
    fields.splice(index, 1);
  }
  $('#json').text(JSON.stringify(fields))
}
///form builder
function buildForm(containerDiv, formObj, is_editable = false) {
  containerDiv.html("");
  if (typeof formObj === 'string') {
    formObj = JSON.parse(formObj);
  }
  if (typeof formObj.fields === 'string') {
    formObj.fields = JSON.parse(formObj.fields);
  }

  var form = $('<form>').addClass('form').attr('action', formObj.action).attr('method', formObj.method).attr('id', formObj.id).data('mode', formObj.mode).data('operation', formObj.operation).data('id', formObj.record_id);
  var mainRow = $('<div>').addClass('row');
  form.append(mainRow)
  containerDiv.append(form)

  fields = formObj.fields.filter(value => value !== null);
  fields.forEach(field => {
    buildFormElement(mainRow, field, is_editable)
  });
  var submitBtn = $('<input/>', {
    type: 'submit',
    value: 'Submit',
    class: 'btn btn-success mt-3',
    disabled: is_editable
  });
  mainRow.append(submitBtn);

}
function generateCard(file, card_body) {
  const card = $("<div>");
  card.addClass("card mb-3");

  const cardBody = $("<div>");
  cardBody.addClass("card-body");




  cardBody.append( card_body);
  card.append(cardBody);

  return card;
}

function buildFormElement(form, field, is_editable = false, in_modal) {
  if (!field) {
    return "";
  }
  const divFormGroup = createFormGroup(field.size, is_editable);
  const label = (field.type == 'items') ? "" : createLabel(field.label);
  let input;
  
  if (field.type === 'media') {
    input = createMediaInput(field);
  } else if (field.type === 'select') {
    input = createSelectInput(field);
  } else if (field.type === 'entity') {
    input = createEntityInput(field);
  } else if (field.type === 'icon') {
    input = createIconsInput(field);
  } else if (field.type === 'checkbox') {
    input = createCheckBoxesInput(field);
  } else if (field.type === 'switches') {
    input = createSwitchesInput(field);
  } else if (field.type === 'radioButton') {
    input = createRadioInput(field);
  } else if (field.type === 'textarea') {
    input = createTextAreaInput(field);
  } else if (field.type === 'content') {
    input = createContentInput(field);
  } else if (field.type === 'mediacenter') {
    input = createMediaCenterInput(field);
  } else if (field.type === 'items') {
    input = createItemsInput(field);
  }else if (field.type === 'date') {
    input = createDateInput(field);
  }else if (field.type === 'time') {
    input = createTimeInput(field);
  }else if (field.type === 'datetime') {
    input = createDateTimeInput(field);
  } else {
    input = createTextInput(field);
  }
  divFormGroup.append(label, input);
  form.append(divFormGroup);

  if (field.type === 'select') {
    if (input.parents('.modal').length) {
      input.select2({
        allowClear: true,
        dropdownParent: input.parents('.modal')
      })
    } else {
      input.select2({allowClear: true});
    }

  } else if (field.type == 'icon') {

    if (input.parents('.modal').length) {
      input.select2({
        allowClear: true,
        width: "100%",
        templateSelection: iformat,
        templateResult: iformat,
        allowHtml: true,
        dropdownParent: input.parents('.modal')
      });
    } else {
      input.select2({
        allowClear: true,
        width: "100%",
        templateSelection: iformat,
        templateResult: iformat,
        allowHtml: true
      });
    }
  } else if (field.type == 'content') {
   
    
  }else if(field.type == 'entity'){
    let select = input.find('select');
    if (select.parents('.modal').length) {
      select.select2({
        allowClear: true,
        dropdownParent: select.parents('.modal')
      })
    } else {
      select.select2({allowClear: true});
    }

  }
}

function createFormGroup(size, is_editable = false) {
  divElement = $('<div>').addClass('form-group mt-3').addClass(size).addClass('b-form-group');
  if (is_editable) {
    divElement.append(
      `
      <div class ="options">
       <button type="button" class="btn btn-primary edit" > <i class="fas fa-edit"></i> </button>
       <button type="button" class="btn btn-danger delete" > <i class="fas fa-trash-alt"></i> </button>
      </div>
    `
    );
  }
  return divElement;
}

function createLabel(text) {
  return $('<label>').text(text);
}

function createTextInput(field) {
  return $('<input>').attr('type', field.type).addClass('form-control').attr('name', field.name).attr('value', field.defaultValue);
}
function createDateInput(field) {
  return $('<input>').attr('type', field.type).addClass('form-control').attr('name', field.name).attr('value', field.defaultValue);
}
function createTimeInput(field) {
  return $('<input>').attr('type', field.type).addClass('form-control').attr('name', field.name).attr('value', field.defaultValue);
}
function createDateTimeInput(field) {
  return $('<input>').attr('type', 'datetime-local').addClass('form-control').attr('name', field.name).attr('value', field.defaultValue);
}

function createMediaCenterInput(field) {
  const mediaCenter = $('<div>').addClass('media-center-group form-control');
  const gallery = $('<div>').addClass('input-gallery');
  const input = $('<input>').attr('type', 'text').attr('hidden', 'hidden').addClass('form-control').attr('name', field.name).attr('value', field.defaultValue);
  const max = (field.options) ? field.options.max : 1;
  const type = (field.options) ? field.options.type : 'avatar';
  const mediaBtn = $('<button>').attr('type', 'button').addClass('btn btn-secondary').html('<i class="fas fa-upload"></i>').css('margin-top', '10px').data('type', type ?? 'avatar').data('max', max);
  mediaCenter.append(input, mediaBtn, gallery)
  // gallery.append(createInputgalleryImage(field))
  mediaBtn.on('click', function () {
    $('#mediaModal').modal('show');
    $('#mediaModal').data('type', $(this).data('type'));
    $('#mediaModal').data('max', $(this).data('max'));
    mediaChooesdBtn = mediaBtn;
    loadIMediaCetner();
  });

  return mediaCenter;
}
function createSelectInput(field) {
  const select = $('<select>').addClass('form-select').attr('name', field.name);
  if (field.multiple || field.options.relation_type == 'many') {
    select.attr('multiple', 'multiple')
  }
  createSelectableOptions(field, select)
  select.val(field.defaultValue)
  return select;
}
function createEntityInput(field) {
  const row             = $('<div>').addClass('row'); 
  const select          = $('<select>').addClass('form-control entity-select').attr('name', field.name);
  const selectContainer = $('<div>').addClass('col-md-10');
  const href            = $('<a>').attr('id','go-to').attr('target','_blank').addClass('btn btn-success col-2').html('<i class="fas fa-list"></i>');
  const hrefContainer   = $('<div>').addClass('col-md-2');
  if (field.multiple) {
    select.attr('multiple', 'multiple')
  }
  createEntitiyOptions(field, select)
  select.val(field.defaultValue)
  selectContainer.append(select);
  hrefContainer.append(href);
  row.append(selectContainer,hrefContainer);
  return row;
}
function createIconsInput(field) {
  const select = $('<select>').addClass('form-select').attr('name', field.name);
  $.each(icons, function (index, value) {
    // Create option element for each icon
    var option = $('<option>');
    option.val(value);
    option.text(value);
    option.data('icon', value)

    // Append option to select input
    select.append(option);
  });
  select.val(field.defaultValue)


  return select;
}
function createCheckBoxesInput(field) {
  const checkboxes = $('<div>').addClass('checkboxes form-group row ');
  if (field.multiple || field.options.relation_type == 'many') {
    checkboxes.attr('multiple', 'multiple')
  }
  createSelectableOptions(field, checkboxes)
  return checkboxes;
}
function createSwitchesInput(field) {
  const switches = $('<div>').addClass('switches form-group row ');
  if (field.multiple || field.options.relation_type == 'many') {
    switches.attr('multiple', 'multiple')
  }
  createSelectableOptions(field, switches)
  return switches;
}
function createRadioInput(field) {
  const radios = $('<div>').addClass('form-group row ');
  createSelectableOptions(field, radios)
  return radios;
}

function createEntitiyOptions(field, selectableElemet) {

  if(field.options){

    $.each(field.options.array, function (index, elementOption) {
      const optionElem = $('<option>').attr('value', elementOption.value).text(elementOption.key).data('url',elementOption.url);
      selectableElemet.append(optionElem);
    });

  }
}
function createSelectableOptions(field, selectableElemet) {
  if (field.options.type == 'entity') {
    data = {};
    data.field = field;
    data.table = $('[name=table]').val();
    //activate later when you dix the muilt build iusse TODO: 
    // $.ajax({
    //   type: "POST",
    //   url: builder_links.selectables,
    //   data: data,
    //   dataType: "dataType",
    //   success: function (response) {
    //     $.each(response.options, function (index, elementOption) { 
    //       createSelectableOption(field,selectableElemet,elementOption)
    //     });
    //   }
    // });
  } else if (field.options.type == 'options') {
    $.each(field.options.array, function (index, elementOption) {
      createSelectableOption(field, selectableElemet, elementOption)
    });
  }
}
function createSelectableOption(field, selectableElemetOption, elementOption) {
  if (field.type == 'select') {
    createSelectOption(field, selectableElemetOption, elementOption);
  } else if (field.type == 'checkbox') {
    createCheckBoxOption(field, selectableElemetOption, elementOption);
  } else if (field.type == 'radioButton') {
    createRaioBoxOption(field, selectableElemetOption, elementOption);
  } else if (field.type == 'switches') {
    createSwitchOption(field, selectableElemetOption, elementOption);
  }
}
function createSelectOption(field, select, option) {
  const optionElem = $('<option>').attr('value', option.value).text(option.key);
  select.append(optionElem);
}
function createCheckBoxOption(field, checkboxes, option) {
  const checkbox = $('<div>').addClass('form-check col-md-4');
  const input = $('<input>').addClass('form-check-input').attr('value', option.value).attr('type', 'checkbox').attr('id', 'id_' + field.name + "_" + option.value).attr('name', field.name);
  const label = $('<label>').addClass('form-check-label').attr('for', 'id_' + field.name + "_" + option.value).text(option.key);
  if (field.multiple || field.options.relation_type == 'many') {
    if (field.defaultValue.includes(option.value)) {
      input.attr("checked", true)
    }
  } else {
    if (field.defaultValue == option.value) {
      input.attr("checked", true)
    }
  }
  checkbox.append(input, label)
  checkboxes.append(checkbox)
}
function createRaioBoxOption(field, radios, option) {
  const radio = $('<div>').addClass('form-check col-md-4');
  const input = $('<input>').addClass('form-check-input').attr('value', option.value).attr('type', 'radio').attr('id', 'id_' + field.name + "_" + option.value).attr('name', field.name);
  const label = $('<label>').addClass('form-check-label').attr('for', 'id_' + field.name + "_" + option.value).text(option.key);
  if (field.multiple || field.options.relation_type == 'many') {
    if (field.defaultValue.includes(option.value)) {
      input.attr("checked", true)
    }
  } else {
    if (field.defaultValue == option.value) {
      input.attr("checked", true)
    }
  }
  radio.append(input, label)
  radios.append(radio)

}
function createSwitchOption(field, switches, option) {
  const switchElement = $('<div>').addClass('form-check form-switch col-md-4');
  const input = $('<input>').addClass('form-check-input').attr('value', option.value).attr('type', 'checkbox').attr('id', 'id_' + field.name + "_" + option.value).attr('role', 'switch').attr('name', field.name);
  const label = $('<label>').addClass('form-check-label').attr('for', 'id_' + field.name + "_" + option.value).text(option.key);
  if (field.multiple || field.options.relation_type == 'many') {
    if (field.defaultValue.includes(option.value)) {
      input.attr("checked", true)
    }
  } else {
    if (field.defaultValue == option.value) {
      input.attr("checked", true)
    }
  }
  switchElement.append(input, label)
  switches.append(switchElement)
}

function createTextAreaInput(field) {
  const textarea = $('<textarea>').addClass('form-control').attr('text', field.defaultValue).attr('id', field.name).attr('name', field.name);
  textarea.html(field.defaultValue);
  //set default val
  return textarea;
}
function createContentInput(field) {
  const content = $('<div>').addClass('form-control').attr('text', field.defaultValue).attr('id', field.name).attr('name', field.name);
  content.html(field.defaultValue)
  card = generateCard(field, content)

  //set default val
  return card;
}
function createItemsInput(field) {
  var container = document.createElement('div');
  container.classList.add('mt-3', 'items-container');
  if (field.options) {
    container.setAttribute('data-items-from', field.options.from);
    container.setAttribute('data-items-on', field.options.on);
    container.setAttribute('data-items-display', field.options.display);
    container.setAttribute('data-items-orderable', field.orderable);
    container.setAttribute('data-items-name', field.name);
  }


  var title = document.createElement('h3');
  title.innerText = field.name
  var createNewBtn = document.createElement('button');
  createNewBtn.classList.add("btn", "btn-success", 'create-new');
  createNewBtn.innerHTML = '<i class="fas fa-plus"></i>';
  if (field.addNotAllowed) {
    createNewBtn = "";
  }
  var hr = document.createElement('hr');

  // Create the table
  var table = document.createElement('table');
  table.classList.add('table', 'table-bordered', 'bg-white', 'text-center');

  // Create the table head
  var thead = document.createElement('thead');
  var tr = document.createElement('tr');
  if (field.options && field.options.display) {
    field.options.display.forEach(function (columnName) {
      var th = document.createElement('th');
      th.textContent = columnName;
      tr.appendChild(th);
    });
    var th = document.createElement('th');
    th.textContent = 'Actions';
    tr.appendChild(th);
  }
  thead.appendChild(tr);
  table.appendChild(thead);

  // Create the table body
  var tbody = document.createElement('tbody');
  // create the table rows
  if (field.defaultValue) {
    $.each(field.defaultValue, function (indexInArray, rowData) {
      let tr = createRaw(field.options.display, rowData, rowData.action_type, rowData.edit_url, rowData.delete_url)
      $(tbody).append(tr);
    });
  }
  table.appendChild(tbody);

  // Return the generated table
  container.append(title, createNewBtn, hr, table);
  if(field.orderable){
    $(table).tableDnD();
  }

  return container;
}
//delete the input functions

$(document).on('click', '.form-group .options .delete', function () {
  $(this).parents('.form-group').addClass('editing');

  $('#builder .b-form-group').each(function (index, element) {
    if ($(this).hasClass('editing')) {
      eIndex = index
    }
  });
  updateFormJSON('delete', eIndex)
  form = {};
  form.fields = fields;
  form.submitBtnText = 'submit';
  buildForm($('#builder'), form, true)
});
$(document).on('click', '.form-group .options .edit', function () {
  $(this).parents('form').find('.form-group.editing').removeClass('editing');
  $(this).parents('.form-group').addClass('editing');
  $('#builder .b-form-group').each(function (index, element) {
    if ($(this).hasClass('editing')) {
      eIndex = index
    }
  });

  $('#editModal').modal('show')
  setJsonToModal(fields[eIndex])
});


$('#editModal input, #editModal select').change(function (e) {
  e.preventDefault();
  updateDefaultValueInput();

});
$(document).on('click', '#editModal button', function (e) {
  e.preventDefault();
  restJsonForCurrentFelid();
});


function updateDefaultValueInput() {
  field = getJsonFromModal();

  $('#defaultValue').html("");
  if(field.type != 'content'){
    buildFormElement($('#defaultValue'), field, false, true)
  }
}
function restJsonForCurrentFelid() {
  updateFormJSON('update', eIndex, getJsonFromModal());
  setModalHtml(fields[eIndex]);
  form = {};
  form.fields = fields;
  form.submitBtnText = 'submit';
  buildForm($('#builder'), form, true)
}
function setModalHtml(field) {
  $('.data-customization').hide();

  type = $('#type').val();
  if (selectables.includes(type)) {
    setModalHtmlSelectable();
  }
  if (type == 'mediacenter') {
    setModalHtmlMediaCenter();
  }
  if (type == 'items') {
    setModalHtmlItems();
  }
  field = Object.assign({}, field);
  field['name'] = 'defaultValue';
  // default value
  $('#defaultValue').html("")

  if(field.type != 'content'){
    buildFormElement($('#defaultValue'), field, false, true)
  }
}
function setModalHtmlSelectable() {
  //show main component
  $('.selectable').show();

  //trigger to show and hide others
  $('#select_src').trigger('change');
  $('#relation_type').trigger('change');
  $('#relation_with').trigger('change');

}
function setModalHtmlMediaCenter() {
  //show main component
  $('.mediacenter').show();

  $('#mediacenter_type').trigger('change');
}
function setModalHtmlItems() {
  //show main component
  $('.items').show();
  $('#items_from').trigger('change');
}
function setJsonToModal(field) {
  $("#editModal input[type='checkbox']").prop("checked", false);

  $('#editModal [id=label]').val(field.label).trigger('change')
  $('#editModal [id=name]').val(field.name).trigger('change')
  $('#editModal [id=type]').val(field.type).trigger('change')
  $('#editModal [id=size]').val(field.size).trigger('change')
  $('#editModal [id=place_holder]').val(field.place_holder).trigger('change')
  $('#editModal [id=hint]').val(field.hint).trigger('change')
  $('#editModal [id=permissions]').val(field.permissions).trigger('change')
  $('#editModal [name=defaultValue]').val(field.defaultValue).trigger('change')
  $('#editModal [name=customValidation]').val(field.customValidation).trigger('change')
  $('#editModal [id=multiple]').prop('checked', field.multiple).trigger('change')
  $('#editModal [id=multipleLangs]').prop('checked', field.multipleLangs).trigger('change')
  $('#editModal [id=langs]').prop('checked', field.langs).trigger('change')
  $('#editModal [id=orderable]').prop('checked', field.orderable).trigger('change')
  $('#editModal [id=required]').prop('checked', field.required).trigger('change')
  $('#editModal [id=unique]').prop('checked', field.unique).trigger('change')
  if (selectables.includes(field.type)) {
    setOptionsToModal(field.options)
  }
  switch (field.type) {
    case 'mediacenter':
      field.options = setMediaCenterDataToModal(field.options);
      break;
    case 'items':
      field.options = setItemsDataToModal(field.options);
      break;

    default:
      break;
  }
  setModalHtml(field);
}
function getJsonFromModal() {
  field = {
    "label": $('#editModal [id=label]').val(),
    "name": $('#editModal [id=name]').val(),
    "type": $('#editModal [id=type]').val(),
    "size": $('#editModal [id=size]').val(),
    "place_holder": $('#editModal [id=place_holder]').val(),
    "hint": $('#editModal [id=hint]').val(),
    "multiple": $('#editModal [id=multiple]').is(':checked'),
    "langs": $('#editModal [id=multi-lang]').is(':checked'),
    "orderable": $('#editModal [id=orderable]').is(':checked'),
    "required": $('#editModal [id=required]').is(':checked'),
    "unique": $('#editModal [id=unique]').is(':checked'),
    "multipleLangs": $('#editModal [id=multipleLangs]').is(':checked'),
    "defaultValue": $('#editModal [name=defaultValue]').val(),
    "customValidation": $('#editModal [name=customValidation]').val(),
    "permissions": $('#editModal [name=permissions]').val(),
  }
  if (selectables.includes(field.type)) {
    field.options = getOptionsFromModal()
  }
  switch (field.type) {
    case 'mediacenter':
      field.options = getMediaCenterDataFromModal();
      break;
    case 'items':
      field.options = getItemsDataToModal();
      break;

    default:
      break;
  }
  return field
}
function setMediaCenterDataToModal(options) {
  $('#mediacenter_max').val(options.max).trigger('change');
  $('#mediacenter_type').val(options.type).trigger('change');
}
function getMediaCenterDataFromModal() {
  data = {};
  data['type'] = $('#mediacenter_type').val();
  data['max'] = $('#mediacenter_max').val();
  return data;
}
function setItemsDataToModal(options) {
  $('#items_on').val(options.on).trigger('change');
  $('#items_on').data('value',options.on);
  
  $('#items_display').val(options.display).trigger('change');
  $('#items_display').data('value',options.display);

  $('#items_from').val(options.from).trigger('change');
}
function getItemsDataToModal() {
  data = {};
  data['from'] = $('#items_from').val();
  data['on'] = $('#items_on').val();
  data['display'] = $('#items_display').val();
  return data;
}
function setOptionsToModal(options) {
  
  $('#OptionsSrc table tbody').html("")
  $('#select_src').val(options.type);
  if (options.type == 'options') {
    options.array.forEach(option => {
      $('#OptionsSrc table tbody').append(`
      <tr>
      <td><input class="form-control" name="key" value="${option.key}"/></td>
      <td><input class="form-control" name="value" value="${option.value}"/></td>
      <td> <button type="button" class="btn btn-danger delete-option">delete</button></td>
      </tr>
      `);
    });
  } else if (options.type == 'entity') {

    $('[name=relation_type]').val(options.relation_type)
    $('[name=relation_type]').data('value',options.relation_type)

    $('[name=relation_with]').val(options.relation_with)
    $('[name=relation_with]').data('relation_with',options.relation_with)

    $('[name=relation_display]').val(options.relation_display)
    $('[name=relation_display]').data('value',options.relation_display)

    $('[name=relation_on]').val(options.relation_on)
    $('[name=relation_on]').data('value',options.relation_on)

    $('[name=pivot_table]').val(options.pivot_table)
    $('[name=pivot_table]').data('value',options.pivot_table)

  }

  restJsonForCurrentFelid()
}
function getOptionsFromModal() {
  options = {};
  options.type = $('#select_src').val();

  if (options.type == 'options') {
    options.array = [];
    $('#OptionsSrc table tbody tr').each(function (index, element) {
      // element == this
      item = {};
      item['key'] = $(this).find('[name=key]').val();
      item['value'] = $(this).find('[name=value]').val();
      options.array.push(item);
    });
    return options;
  } else if (options.type == 'entity') {
    options.relation_type = $('[name=relation_type]').val()
    options.relation_with = $('[name=relation_with]').val()
    options.relation_on = $('[name=relation_on]').val()
    options.relation_display = $('[name=relation_display]').val()
    options.pivot_table = $('[name=pivot_table]').val()
  }

  return options;
}


var editModal = document.getElementById('editModal');
if (editModal) {
  editModal.addEventListener('hide.bs.modal', function (event) {
    restJsonForCurrentFelid();
    $('.editing').removeClass('editing')
  })

}

//options table control
$('#add-option').click(function (e) {
  e.preventDefault();
  key = $('[name=src_key]').val();
  value = $('[name=src_value]').val();
  $('#OptionsSrc table tbody').append(`
    <tr>
      <td><input class="form-control" name="key" value="${key}"/></td>
      <td><input class="form-control" name="value" value="${value}"/></td>
      <td> <button type="button" class="btn btn-danger delete-option">delete</button></td>
    </tr>
  `);
  $('[name=src_key]').val("");
  $('[name=src_value]').val("");

});
$(document).on('click', '.delete-option', function (e) {
  e.preventDefault();
  $(this).parents('tr').remove()
  restJsonForCurrentFelid();
});
$('#select_src').change(function (e) {
  e.preventDefault();
  $('.select-src').hide();
  let showClass = $('#select_src').val()
  if (showClass.length) {
    $(`.select-src.${showClass}`).show();
  }
});
$('#relation_type').change(function (e) {
  e.preventDefault();
  $('.relation_type').hide();
  let showClass = $('#relation_type').val()
  if (showClass.length) {
    $(`.relation_type.${showClass}`).show();
  }
});

//media center control
$('#mediacenter_type').change(function (e) {
  e.preventDefault();
  if (['avatar', 'image'].includes($(this).val())) {
    $('#mediacenter_max').val(1);
    $('#mediacenter_max').attr('disabled', true);
  } else {
    $('#mediacenter_max').attr('disabled', false);
  }

});


$('[name=name]').change(function (e) {
  e.preventDefault();

  if ($('[name=slug]').val() === "") {
    $('[name=slug]').val(slugify($(this).val()))
  }
  if ($('[name=table]').val() === "") {
    $('[name=table]').val(tablefy($(this).val()))
  }
  if ($('[name=model]').val() === "") {
    $('[name=model]').val(modelfy($(this).val()))
  }
});

$('#saveEntity').click(function (e) {
  e.preventDefault();
  $('#saveEntityModal').modal('toggle');
});

$('#entityForm').submit(function (e) {
  e.preventDefault();
  let data = {};
  data['name']              = $('[name=name]#name').val();
  data['slug']              = $('[name=slug]#slug').val();
  data['table']             = $('[name=table]').val();
  data['operations']        = $('[name=operations]').val();
  data['package']           = $('[name=package]').val();
  data['model']             = $('[name=model]').val();
  data['json']              = $('[name=json]').val();
  data['table_fields']      = $('[name=table_fields]').val();
  data['table_filters']     = $('[name=table_filters]').val();
  data['api_fields_list']   = $('[name=api_fields_list]').val();
  data['api_fields_single'] = $('[name=api_fields_single]').val();
  data['orderable']         = $('[name=orderable]').val();
  data['orderable_key']     = $('[name=orderable_key]').val();

  let url = $(this).attr('action');
  $.ajax({
    type: "POST",
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      $('.loader-rm-wrapper').removeClass('hide-loader')

    },
    success: function (response) {
      toastr.success(response.message);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      response = JSON.parse(jqXHR.responseText); 
      toastr.error(response.message);
      $.each(response.errors, function (key, array) { 
         $.each(array, function (index, error) { 
          toastr.error(error,key);
         });
      });
    },
    complete: function () {
      $('.loader-rm-wrapper').addClass('hide-loader')

    }
  });
});

//save
$(document).on('submit', '#operation-form,.operation-form', function (e) {
  e.preventDefault();
  let form      = $(this);
  let data      = getFormData($(this));
  let itemsData = getItemsData(form);
  for (let key in itemsData) {
    data[key] = itemsData[key];
  }

  let url = $(this).attr('action');
  let type = $(this).data('mode') == "edit" ? 'PUT' : 'POST';
  let mode = $(this).data('mode');
  $.ajax({
    type: type,
    url: url,
    data: data,
    dataType: "json",
    beforeSend: function () {
      // Code to run before the request is sent
     $('.loader-rm-wrapper').removeClass('hide-loader')
    },
    success: function (response) {
      toastr.success(response.message);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      // Code to run if the request fails
      response = JSON.parse(jqXHR.responseText); 
      toastr.error(response.message);
      $.each(response.errors, function (key, array) { 
         $.each(array, function (index, error) { 
          toastr.error(error,key);
         });
      });
    },
    complete: function () {
      $('.loader-rm-wrapper').addClass('hide-loader')
    }
  });
});

$(document).ready(function () {
  $('.advance-select').each(function (index, element) {
    let name = $(this).attr('name')+"...";
    if ($(this).parents('.modal').length) {
      let id = $(this).parents('.modal').first().attr('id');
      $(this).select2({
        allowClear: true,
        placeholder:name,
        dropdownParent: $('#'+id),
        placeholder:"Select an option",
      }).trigger('change');
    } else {
      $(this).select2({
        allowClear: true,
        placeholder:name,
        placeholder:"Select an option",

      }).trigger('change');
    }
  });
  
});

$('.select-all-btn').click(function (e) {
  e.preventDefault();
  $(this).parent('div').find('select').each(function (index, element) {
    let values = [];
    $(this).find('option').each(function (index, element) {
      values.push($(this).attr('value'))
    })
    $(this).val(values).trigger('change')
  })
});
$('.un-select-all-btn').click(function (e) {
  e.preventDefault();
  $(this).parent('div').find('select').each(function (index, element) {

    $(this).val(null).trigger('change')
  })
});

function slugify(text) {
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '-') // Replace spaces with -
    .replace(/[^\w\-]+/g, '') // Remove all non-word chars
    .replace(/\-\-+/g, '-') // Replace multiple - with single -
    .replace(/^-+/, '') // Trim - from start of text
    .replace(/-+$/, ''); // Trim - from end of text
}

function tablefy(text) {
  return text
    .toString()
    .toLowerCase()
    .replace(/\s+/g, '_') // Replace spaces with -
    .replace(/[^\w\-]+/g, '') // Remove all non-word chars
    .replace(/\-\-+/g, '_') // Replace multiple - with single -
    .replace(/^-+/, '') // Trim - from start of text
    .replace(/-+$/, ''); // Trim - from end of text
}

function modelfy(text) {
  return text
    .split(" ") // Split the string into an array of words
    .map(word => {
      return word.charAt(0).toUpperCase() + word.slice(
        1); // Capitalize the first letter of each word
    })
    .join(""); // Join the array back into a string with no spaces
}
function getFormData(form) {
  // Initialize an empty object to store the form data
  var formData = {};

  // Iterate over each form input
  form.find('input, select, textarea').each(function () {
    var input = $(this);
    var name = input.attr('name');
    var value = input.val();

    // Handle checkboxes (multiple values)
    if (input.is(':checkbox')) {
      let multiple = input.parents('.form-group.row').attr('multiple')
      if (input.is(':checked')) {
        // Add the checkbox value to the formData object as an array
        if (multiple == 'multiple') {
          if (!formData[name]) {
            formData[name] = [];
          }
          formData[name].push(value);
        } else {
          formData[name] = value;

        }

      }
    }
    // Handle radio buttons (single value)
    else if (input.is(':radio')) {
      if (input.is(':checked')) {
        // Add the radio button value to the formData object
        formData[name] = value;
      }
    }
    // Handle other input types (text, select, textarea)
    else {
      // Add the input value to the formData object
      formData[name] = value;
    }
  });
  $('div[name] ').each(function () {
    let name        = $(this).attr('name');
    formData[name] = $(this).find('.ql-editor').html();
})
  return formData;
}
$('#relation_with').change(function (e) {
  e.preventDefault();
  id = $(this).val();
  $.ajax({
    type: "GET",
    url: builder_links.get_fields,
    data: { id: id },
    dataType: "json",
    success: function (data) {
      let relation_on       = $('[name=relation_on]').val();
      let relation_display  = $('[name=relation_display]').val();
      if(relation_on == null){
        relation_on       = $('[name=relation_on]').data('value');
      }
      if(relation_display == null){
        relation_display  = $('[name=relation_display]').data('value');
      }
      ;
      $("#relation_on").empty();
      $("#relation_display").empty();
      $.each(data, function (indexInArray, value) {
        const newOption = new Option(value, value);
        const newOption2 = new Option(value, value);
        $("#relation_on").append(newOption).trigger("change");
        $("#relation_display").append(newOption2).trigger("change");
      });
      $('#relation_on').val(relation_on)
      $('#relation_display').val(relation_display)
    }
  });
})
$('#items_from').change(function (e) {
  e.preventDefault();
  id = $(this).val();
  $.ajax({
    type: "GET",
    url: builder_links.get_fields,
    data: { id: id },
    dataType: "json",
    success: function (data) {
      let items_on      = $('#items_on').val();
      let items_display = $('#items_display').val();
      if(items_on == null){
        items_on       = $('#items_on').data('value');
      }
      if(items_display == null){
        items_display       = $('#items_display').data('value');
      }
      ;
     
      $("#items_on").empty();
      $("#items_display").empty();
      $.each(data, function (indexInArray, value) {
        const newOption = new Option(value, value);
        const newOption2 = new Option(value, value);
        $("#items_on").append(newOption).trigger("change");
        $("#items_display").append(newOption2).trigger("change");
      });
      $('#items_on').val(items_on).trigger("change");
      ;
      $('#items_display').val(items_display).trigger("change");
    }
  });
});

function createRaw(display, data, edit_url = "", delete_url = "") {
  if (typeof display == "string") {
    display = display.split(",");
  }

  let tr = $('<tr>').data('id', data['id']).data('type', type).data('url', edit_url).data('data', data)
  $.each(display, function (indexInArray, valueOfElement) {
    let newTd = $('<td>').html(data[valueOfElement]);
    tr.append(newTd);
  });

  let td = $('<td>');
  let editButton    =  $('<button>').addClass('btn-operation edit-item').html('<i class="fas fa-pencil-alt"></i>');
  let deleteButton  = $('<button>').addClass('btn-operation delete-item').html(' <i class="fas fa-trash"></i>').data('href', delete_url);
  if(edit_url.length){
    td.append(editButton);
  }
  td.append(deleteButton);
  tr.append(td);
  return tr;
}
$(".current-fields").on('select2:opening', function (e) {
  let select = $(this);
  let value = select.val();
  select.data('value', value)
  select.empty();
  fields.forEach(function (item) {
    const newOption = new Option(item.label, item.name);
    select.append(newOption)
  })

  select.val(value)

});
$(document).on('change', 'input[type=checkbox]', function () {
  let multiple = $(this).parents('.form-group.row').attr('multiple')
  let checked = $(this).prop("checked");
  if (multiple != "multiple") {
    $(`[name=${$(this).attr('name')}]`).prop("checked", false)
    $(this).prop("checked", checked);
  }
})

$(document).on('change','.entity-select',function (e) {
  let val = $(this).find('option:selected').data('url')
  $(this).parentsUntil('.row').parent().find('a#go-to').attr('href',val)
})