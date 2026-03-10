"use strict";

var datatable;
var cols;
var KTUsersList = function () {
    // Define shared variables
    var table = document.getElementById('view-datatable');
    var toolbarBase;
    var toolbarSelected;
    var selectedCount;
    var searchValue;
    var filtersValues;

    // Private functions
    var initTable = function () {
        cols = [];
        $('table#view-datatable thead th').each(function (index, element) {
            let data        = $(this).data('name');
            let name        = (index == 0) ? data : $(this).text();
            let col         = { 'name': name, 'data': data  };
            if($(this).attr('orderable')){
                col.orderable = ($(this).attr('orderable') == true);
            }
            cols.push(col);
        });
        let filters = {};
        $('select.filter-input,input.filter-input').each(function (index, element) {
            let name = $(this).attr('name');
            let value = $(this).val();
            filters[name] = value;
        });        

        filtersValues = filters;
        let url = $('table#view-datatable').data('load');
        if(!$.fn.DataTable.isDataTable('#view-datatable')){            
            datatable = $('table#view-datatable').DataTable({
                dom:   "<'d-flex justify-content-between align-items-center mb-2'<'dt-buttons'B><'dt-length'l>>" +
                "frtip",
                lengthMenu: [ [25, 50, 100, 300, 500,-1], [25, 50, 100, 300, 500,trans('all')] ],
                pageLength: 25, // default selected value
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                searchDelay: 500,
                searching: false,
                processing: true,
                serverSide: true,
                order: [[1, 'desc']],
                stateSave: true,
                ajax: {
                    url: url,
                    type: 'POST',
                    data: function (data) {
                        data['search'] = searchValue;
                        data['filters'] = filtersValues;
                        return data;
                    },
                    error: function (xhr, error, thrown) {
                        // Handle AJAX errors
                        console.log(xhr, error, thrown);
                        
                        // alert('noq An error occurred: ' + error);
                    }

                },
                columnDefs: [
                    { orderable: false, targets: 0 }, // Disable ordering on column 0 (checkbox)
                    { orderable: false, targets: (cols.length - 1) }, // Disable ordering on column 6 (actions)                
                ],
    
                columns: cols
            });
            
            // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
            datatable.on('draw', function () {
                
                initToggleToolbar();
                handleDeleteRows();
                handleRestoreRows();
                toggleToolbars();
            });

        }
        
    }
    var initVisible = function () {

        var select = $('#visible_cols');

        $.each(cols, function (index, option) {
            select.append($('<option>', {
                value: index,
                text: option.name
            }).prop('selected', true)
            );
        });
        if(select){            
            select.select2()
        }
    }



    // Filter Datatable
    var handleFilterDatatable = () => {
        // Select filter options
        const filterForm = document.querySelector('[data-kt-user-table-filter="form"]');
        const filterButton = filterForm.querySelector('[data-kt-user-table-filter="filter"]');

        // Filter datatable on submit
        filterButton.addEventListener('click', function () {
            let filters = {};
            $('select.filter-input,input.filter-input').each(function (index, element) {
                let name = $(this).attr('name');
                let value = $(this).val();
                filters[name] = value;
            });

            filtersValues = filters;


            datatable.draw();
        });
    }

    // Reset Filter
    var handleResetForm = () => {
        // Select reset button
        const resetButton = document.querySelector('[data-kt-user-table-filter="reset"]');

        // Reset datatable
        resetButton.addEventListener('click', function () {
            // Select filter options
            const filterForm = document.querySelector('[data-kt-user-table-filter="form"]');
            const selectOptions = filterForm.querySelectorAll('select');

            // Reset select2 values -- more info: https://select2.org/programmatic-control/add-select-clear-items
            selectOptions.forEach(select => {
                $(select).val('').trigger('change');
            });

            // Reset datatable --- official docs reference: https://datatables.net/reference/api/search()
            datatable.search('').draw();
        });
    }


    // Delete subscirption
    var handleDeleteRows = () => {
        // Select all delete buttons
        const deleteButtons = table.querySelectorAll('.delete-btn');
        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');
                // set the btn
                const btn = $(this);
                const href = btn.attr('href');
                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to delete this?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            type: "DELETE",
                            url: href,
                            data: {},
                            dataType: "json",
                            success: function (response) {
                                Swal.fire({
                                    text: trans("You have deleted the record!."),
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    // Remove current row
                                    datatable.draw();
                                }).then(function () {
                                    // Detect checked checkboxes
                                    toggleToolbars();
                                });
                            }
                        });

                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: "item was not deleted.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }

    // Restore records
    var handleRestoreRows = () => {
        // Select all restore buttons
        const restoreButtons = table.querySelectorAll('.btn-restore');
        restoreButtons.forEach(r => {
            // Restore button on click
            r.addEventListener('click', function (e) {
                e.preventDefault();

                // Select parent row
                const parent = e.target.closest('tr');
                // set the btn
                const btn = $(this);
                const href = btn.attr('href');
                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: trans("Are you sure you want to restore this?"),
                    icon: "question",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: trans("Yes, restore!"),
                    cancelButtonText: trans("No, cancel"),
                    customClass: {
                        confirmButton: "btn fw-bold btn-success",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            type: "POST",
                            url: href,
                            data: {
                                _method: 'PUT'
                            },
                            dataType: "json",
                            success: function (response) {
                                Swal.fire({
                                    text: trans("You have restored the record!"),
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: trans("Ok, got it!"),
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    // Reload datatable
                                    datatable.draw();
                                }).then(function () {
                                    // Detect checked checkboxes
                                    toggleToolbars();
                                });
                            },
                            error: function (xhr, status, error) {
                                Swal.fire({
                                    text: trans("Failed to restore the record."),
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: trans("Ok, got it!"),
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        });

                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: trans("Item was not restored."),
                            icon: "info",
                            buttonsStyling: false,
                            confirmButtonText: trans("Ok, got it!"),
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }


    // Init toggle toolbar
    var initToggleToolbar = () => {
        // Toggle selected action toolbar
        // Select all checkboxes
        const checkboxes = table.querySelectorAll('[type="checkbox"]');
       
        // Select elements
        toolbarBase = document.querySelector('[data-kt-user-table-toolbar="base"]');
        toolbarSelected = document.querySelector('[data-kt-user-table-toolbar="selected"]');
        selectedCount = document.querySelector('[data-kt-user-table-select="selected_count"]');
        const deleteSelected = document.querySelector('[data-kt-user-table-select="delete_selected"]');

        // Toggle delete selected toolbar
        checkboxes.forEach(c => {
            // Checkbox on click event
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });
        $('thead [type="checkbox"]').change(function(){
            
            var isChecked   = $(this).is(':checked');
            var targetTable = $(this).data('kt-check-target');
            var TableChecks = targetTable;
            
            $(TableChecks).prop('checked', isChecked);
            
        });

        // Deleted selected rows
        deleteSelected.addEventListener('click', function () {
            // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
            Swal.fire({
                text: "Are you sure you want to delete selected records?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: trans("Yes, delete!"),
                cancelButtonText: trans("No, cancel"),
                customClass: {
                    confirmButton: "btn fw-bold btn-primary",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    $('input[name="table_selected"]:checked').each(function () {
                        let checkboxValue = $(this).val();
                        let checkUrl = deleteUrl.replace('%s', checkboxValue)
                        $.ajax({
                            type: "DELETE",
                            url: checkUrl,
                            data: {},
                            dataType: "json",
                            success: function (response) {

                            }
                        });

                    });
                    Swal.fire({
                        text: trans("You have deleted all selected recodes!."),
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: trans("Ok, got it!"),
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {
                        // Remove header checked box
                        const headerCheckbox = table.querySelectorAll('[type="checkbox"]')[0];
                        headerCheckbox.checked = false;
                        datatable.draw();
                        toggleToolbars(); // Detect checked checkboxes
                        initToggleToolbar(); // Re-init toolbar to recalculate checkboxes
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: trans("Selected records was not deleted."),
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: trans("Ok, got it!"),
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });
    }

    // Toggle toolbars
    const toggleToolbars = () => {
        // Select refreshed checkbox DOM elements 
        const allCheckboxes = table.querySelectorAll('tbody [type="checkbox"]');

        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        // Toggle toolbars
        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }
    // Hook export buttons
    var exportButtons = () => {
        const documentTitle = 'Customer Orders Report';
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: $('title').text() + " copy"
                },
                {
                    extend: 'excelHtml5',
                    title: $('title').text() + " excel"
                },
                {
                    extend: 'csvHtml5',
                    title: $('title').text() + " csv"
                },
                {
                    extend: 'pdfHtml5',
                    title: $('title').text() + " html"
                }
            ]
        }).container().appendTo($('#kt_datatable_example_buttons'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll('#kt_datatable_example_export_menu [data-kt-export]');
        exportButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();

                // Get clicked export value
                const exportValue = e.target.getAttribute('data-kt-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

                // Trigger click event on hidden datatable export buttons
                target.click();
            });
        });
    }
    return {
        // Public functions  
        init: function () {
            if (!table) {
                return;
            }

            initTable();
            initVisible();
            initToggleToolbar();
            handleResetForm();
            handleDeleteRows();
            handleRestoreRows();
            handleFilterDatatable();

        }
    }
}();

// On document ready


$('#visible_cols').change(function () {
    $(this).find('option').each(function (index, element) {
        let column = datatable.column(index);
        if ($(this).is(':selected')) {
            column.visible(true)
        } else {
            column.visible(false)
        }
    });
})
$(document).ready(function () {
    KTUsersList.init();
    $('.advance-select').each(function (index, element) {
        let name = $(this).attr('name') + "...";
        if ($(this).parents('.modal').length) {
            let id = $(this).parents('.modal').first().attr('id');
            $(this).select2({
                allowClear: true,
                placeholder: name,
                dropdownParent: $('#' + id),
                width: '100%' // Force width
            }).trigger('change');
        } else {
            $(this).select2({
                allowClear: true,
                placeholder: name,
                width: '100%' // Force width
            }).trigger('change');
        }
    });
    $('#export').click(function (e) {
        e.preventDefault();
        // Step 1: Get all selected options
        var selectedOptions = $('#visible_cols option:selected');
        // Step 2: Extract the text of each selected option into an array
        var selectedTexts = selectedOptions.map(function () {
            return $(this).attr('value');
        }).get();
        // Step 3: Convert the array into a query parameter string
        var queryParams = selectedTexts.map(function (text) {
            return 'cols[]=' + cols[text].data;
        }).join('&');
        let href = $(this).attr('href') + '?' + queryParams;
        window.location.replace(href);
    });


});




$(document).ready(function () {
    // Extract column definitions from the table header
    var columns = [];
    $('.view-datatable thead th').each(function () {
        var column = {
            data: $(this).data('name'), // Use the data-name attribute as the data source
            name: $(this).data('name'), // Use the data-name attribute as the column name
            title: $(this).text().trim(), // Use the text content of the <th> as the column title
            className: $(this).attr('class') // Preserve the class names for styling
        };
        columns.push(column);
    });
    $('.view-dataTable').each(function () {
        let table   = $(this);
        let url     = $(this).data('load');
        let columns = [];
        let filtersValues = {};
        let filters = {};
        $(this).closest('.table-responsive').find('.filter-input').each(function (index, element) {
            let name      = $(this).attr('name');
            let value     = $(this).val();
            filters[name] = value;
        });
        filtersValues = filters;
        // Parse the URL
        const myUrl     = new URL(url);
        const params    = new URLSearchParams(myUrl.search);
        // Filter and log only `filters` parameters
        for (const [key, value] of params.entries()) {
            const match = key.match(/^filters(?:\[(\w+)\])?(?:\[(\d+)\])?$/);
            if (match) {
                const [, group, index] = match;
                if (!filtersValues[group]) {
                    filtersValues[group] = [];
                }
                if (!filtersValues[group].includes(value)) {                        
                    filtersValues[group].push(value);
                }
            }
        }
            

        table.find('thead th').each(function () {
            var column = {
                data: $(this).data('name'), // Use the data-name attribute as the data source
                name: $(this).data('name'), // Use the data-name attribute as the column name
                title: $(this).text().trim(), // Use the text content of the <th> as the column title
                className: $(this).attr('class'), // Preserve the class names for styling
                orderable:$(this).hasClass('orderable')
            };
            columns.push(column);
        });
        
        // Initialize DataTable
        let dataTable = table.DataTable({
            searching: false,
            processing: true,
            serverSide: true,
            dom:   "<'d-flex justify-content-between align-items-center mb-2'<'dt-buttons'B><'dt-length'l>>" +
            "frtip",
            lengthMenu: [ [25, 50, 100, 300, 500,-1], [25, 50, 100, 300, 500,trans('all')] ],
            pageLength: 25, // default selected value
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            ajax: {
                url: url, // Use the data-load attribute for the AJAX URL
                type: 'POST',
                 data: function (data) {
                    data['filters'] = filtersValues;
                    return data;
                },
            },
            columns: columns, // Use the dynamically generated columns
            order: [[0, 'desc']], // Default sorting by the first column
          
        });
        dataTable.on('draw', function () {
            // Select all delete buttons
            table.find('.delete-btn').each(function (index, element) {
                // element == this
                 // Delete button on click
                 element.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Select parent row
                    const parent = e.target.closest('tr');
                    // set the btn
                    const btn = $(this);
                    const href = btn.attr('href');
                    // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Are you sure you want to delete this?",
                        icon: "warning",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: "Yes, delete!",
                        cancelButtonText: "No, cancel",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            $.ajax({
                                type: "DELETE",
                                url: href,
                                data: {},
                                dataType: "json",
                                success: function (response) {
                                    Swal.fire({
                                        text: trans("You have deleted the record!."),
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: trans("Ok, got it!"),
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    }).then(function () {
                                        // Remove current row
                                        dataTable.draw();
                                    })
                                }
                            });

                        } else if (result.dismiss === 'cancel') {
                            Swal.fire({
                                text: "item was not deleted.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    });
                })
            });
            
            // Select all restore buttons
            table.find('.btn-restore').each(function (index, element) {
                // Restore button on click
                element.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Select parent row
                    const parent = e.target.closest('tr');
                    // set the btn
                    const btn = $(this);
                    const href = btn.attr('href');
                    // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                    Swal.fire({
                        text: trans("Are you sure you want to restore this?"),
                        icon: "question",
                        showCancelButton: true,
                        buttonsStyling: false,
                        confirmButtonText: trans("Yes, restore!"),
                        cancelButtonText: trans("No, cancel"),
                        customClass: {
                            confirmButton: "btn fw-bold btn-success",
                            cancelButton: "btn fw-bold btn-active-light-primary"
                        }
                    }).then(function (result) {
                        if (result.value) {
                            $.ajax({
                                type: "POST",
                                url: href,
                                data: {
                                    _method: 'PUT'
                                },
                                dataType: "json",
                                success: function (response) {
                                    Swal.fire({
                                        text: trans("You have restored the record!"),
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: trans("Ok, got it!"),
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    }).then(function () {
                                        // Reload datatable
                                        dataTable.draw();
                                    })
                                },
                                error: function (xhr, status, error) {
                                    Swal.fire({
                                        text: trans("Failed to restore the record."),
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: trans("Ok, got it!"),
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    });
                                }
                            });

                        } else if (result.dismiss === 'cancel') {
                            Swal.fire({
                                text: trans("Item was not restored."),
                                icon: "info",
                                buttonsStyling: false,
                                confirmButtonText: trans("Ok, got it!"),
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    });
                })
            });

        });
      
        
        $(this).closest('.table-responsive').find('.filter-input').on('change', function () {
            $(this).closest('.table-responsive').find('.filter-input').each(function (index, element) {
                let name      = $(this).attr('name');
                let value     = $(this).val();
                filters[name] = value;
            });
            filtersValues = filters;
            const myUrl     = new URL(url);
            const params    = new URLSearchParams(myUrl.search);
            // Filter and log only `filters` parameters
            for (const [key, value] of params.entries()) {
                const match = key.match(/^filters(?:\[(\w+)\])?(?:\[(\d+)\])?$/);
                if (match) {
                    const [, group, index] = match;
                    if (!filtersValues[group]) {
                        filtersValues[group] = [];
                    }
                    if (!filtersValues[group].includes(value)) {                        
                        filtersValues[group].push(value);
                    }
                }
            }            
            dataTable.draw();
        });

    });
   

});




