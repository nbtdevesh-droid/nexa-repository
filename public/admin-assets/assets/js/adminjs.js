///////Show toastr messages////////
$(".admin-toastr").trigger('click');
function toastr_success(msg) {
    toastr.success(msg)
}
function toastr_danger(msg) {
    toastr.error(msg)
}
///////Show toastr messages////////

///////////Summer Notes//////////////
$(function () {
    // Summernote
    $('#content').summernote({ height: 100 });
    $('#content').summernote('reset');

});

$.validator.addMethod("string", function(value, element) {
    return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
}, "Please enter only letters");
//===============================================category============================================================//
$('#add_new_category_form').validate({
    rules: {
        category_icon: {
            required: {
                depends: function (elem) {
                    var fomrs_id = $(this).parents("form").attr("id");
                    return fomrs_id != 'edit_category_form';
                }
            }
        },
        category_name: {
            required: true,
            category_name_rule: true
        },
        category_status: {
            required: true
        },
        banner_image: {
            required: {
                depends: function (elem) {
                    var form_id = $(this).parents("form").attr("id");
                    return form_id != 'edit_category_form' && $('#parent_id').val() === ''; // banner_image required when parent_id is blank
                }
            }
        },
        category_order: {
            required: true,
            number: true,
            categoryOrderUnique: true
        }
    },
    messages: {
        category_icon: {
            required: "Please choose a category image."
        },
        category_name: {
            required: "Please enter Category Name."
        },
        category_status: {
            required: "Please Select Status."
        },
        banner_image: {
            required: "Please upload a banner image."
        },
        category_order: {
            required: "Please enter a category order.",
            number: "The category order must be a number.",
            categoryOrderUnique: "The category order must be unique."
        }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    }
});

//==============================================Brand================================================================//
$('#add_new_brand_form').validate({
    rules: {
        brand_name: {
            required: true,
            brand_name_rule: {
                depends: function (elem) {
                    var fomrs_id = $(this).parents("form").attr("id");
                    return fomrs_id != 'edit_brand_form';
                }
            }
        },
        brand_status: {
            required: true
        }

    },
    messages: {
        brand_name: {
            required: "Please enter Brand Name."
        },
        brand_status: {
            required: "Please Select Status."
        }

    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    }
});

$.validator.addMethod('category_name_rule', function (value, element) {
    var category_id = $('.hidden_category_id').val() || null;
    
    var isValid = false;

    $.ajax({
        url: base_url + 'unique-category-name',
        method: 'POST',
        data: { 
            existance_type: 'category_name', 
            category_name: value, 
            category_id: category_id,
            _token: csrf_token 
        },
        dataType: 'json',
        async: false,
        success: function (response) {
            isValid = response.valid;
        }
    });

    return isValid;
}, 'Category name is already taken.');


// $.validator.addMethod('categoryOrderUnique', function (value, element) {
//     var urlss = base_url + 'unique-category-order';
//     var form_id = $('.hidden_category_id').val();
//     var parent_id = $('#parent_id').val();
//     var isUnique = false;
  
//     $.ajax({
//         url: urlss,
//         method: 'POST',
//         data: { 
//             existance_type: 'category_order', 
//             category_order: value,
//             parent_id: parent_id, 
//             form_id: form_id, 
//             _token: csrf_token 
//         },
//         async: false,
//         success: function (response) {
//             isUnique = response.is_unique;
//         },
//         error: function() {
//             isUnique = false;
//         }
//     });
  
//     return isUnique;
// }, 'Category order is already taken.');

$.validator.addMethod('categoryOrderUnique', function (value, element) {
    var urlss = base_url + 'unique-category-order';
    var form_id = $('.hidden_category_id').val();
    var parent_id = $('#parent_id').val();
    var isUnique = false;
  
    if (form_id) {
        var currentCategoryOrder = $(element).data('current-order');
        if (value === currentCategoryOrder) {
            isUnique = true;
        } else {
            $.ajax({
                url: urlss,
                method: 'POST',
                data: { 
                    existance_type: 'category_order', 
                    category_order: value,
                    parent_id: parent_id, 
                    form_id: form_id, 
                    _token: csrf_token 
                },
                async: false,
                success: function (response) {
                    isUnique = response.is_unique;
                },
                error: function() {
                    isUnique = false;
                }
            });
        }
    } else {
        $.ajax({
            url: urlss,
            method: 'POST',
            data: { 
                existance_type: 'category_order', 
                category_order: value,
                parent_id: parent_id, 
                _token: csrf_token 
            },
            async: false,
            success: function (response) {
                isUnique = response.is_unique;
            },
            error: function() {
                isUnique = false;
            }
        });
    }
  
    return isUnique;
  }, 'Category order is already taken.');

$.validator.addMethod('brand_name_rule', function (value, element) {
    var urlss = base_url + 'unique-brand-name';
    $.ajax({
        url: urlss,
        method: 'POST',
        data: { existance_type: 'brand_name', brand_name: value, _token: csrf_token },
        async: false,
        success: function (response) {
            if (response !== 'true') {
                res = false;
            } else {
                res = true;
            }
        }
    });
    return res;
}, 'Brand name is already taken.');

$('.modal').on('hidden.bs.modal', function () {
    if ($(this).find('form').attr("id") == 'add_new_brand_form' || $(this).find('form').attr("id") == 'edit_brand_form') {
        $(this).find('form').attr("id", "add_new_brand_form");
        $(this).find('form').attr("action", base_url + 'brand/store');
        $("#modal-default .modal-header .modal-title").html('Add New Brand');
        $('.hidden_brand_id').remove();

        var $brandform = $('#add_new_brand_form');
        $brandform.validate().resetForm();
        $(this).find('form').trigger('reset');
        $brandform.find('input[name="_method"]').remove();
        $brandform.find('.error').removeClass('error');
        $brandform.find('.is-invalid').removeClass('is-invalid');
    }

    if ($(this).find('form').attr("id") == 'add_new_category_form' || $(this).find('form').attr("id") == 'edit_category_form') {
        $(this).find('form').attr("id", "add_new_category_form");
        $(this).find('form').attr("action", base_url + 'category/store');
        $("#modal-default .modal-header .modal-title").html('Add New Category');
        $('.hidden_category_id').remove();

        var $catform = $('#add_new_category_form');
        $catform.validate().resetForm();
        $(this).find('form').trigger('reset');
        $catform.find('input[name="_method"]').remove();
        $catform.find('.error').removeClass('error');
        $catform.find('.is-invalid').removeClass('is-invalid');
        $('.show_cat_icon').attr('src', "");
    }
});

//////////Open popup for Edit category////////////

$("body").on("click", ".admin_edit_brand", function (e) {
    e.preventDefault();
    var brand_id = $(this).attr('brand_id');
    var brand_name = $(this).attr('brand_name');
    var brand_status = $(this).attr('brand_status');
    $('.modal_brand').modal({ backdrop: 'static' });

    //Change From_id and Form_Action
    var form = $('#modal-default').find('form');
    form.attr("id", "edit_brand_form");
    form.attr("action", base_url + 'brand/' + brand_id);

    // Add @method('put')
    var methodInput = '<input type="hidden" name="_method" value="PUT">';
    form.find('input[name="_method"]').remove(); // Remove any existing method input to avoid duplicates
    form.prepend(methodInput);

    $("#modal-default .modal-header .modal-title").html('Edit Brand');
    //Add hidden field for subadmin ID
    var input_hidden = '<input type="hidden" name="hidden_brand_id" value=' + brand_id + ' class="hidden_category_id">';
    $(".brand_name").after(input_hidden);
    $("#brand_status option[value='" + brand_status + "']").attr("selected", "selected");
    $('#savedata').val("Update");
    $('.brand_name').val(brand_name);

    $('#modal-default').on('hidden.bs.modal', function (e) {
        e.preventDefault();
        location.reload();
    })

});

$("body").on("click", ".admin_edit_category", function (e) {
    e.preventDefault();
    var category_id = $(this).attr('category_id');
    var category_name = $(this).attr('category_name');
    var parent_category = $(this).attr('parent_category');
    var category_icon_path = $(this).attr('category_icon_path');
    var category_icon = $(this).attr('category_icon');
    var category_banner_path = $(this).attr('category_banner_path');
    var category_banner = $(this).attr('category_banner');
    var category_status = $(this).attr('category_status');
    var category_order = $(this).attr('category_order');

    $('.modal_category').modal({ backdrop: 'static' });
    //Change From_id and Form_Action
    var form = $('#modal-default').find('form');
    form.attr("id", "edit_category_form");
    form.attr("action", base_url + 'category/' + category_id);

    // Add @method('put')
    var methodInput = '<input type="hidden" name="_method" value="PUT">';
    form.find('input[name="_method"]').remove(); // Remove any existing method input to avoid duplicates
    form.prepend(methodInput);

    $("#modal-default .modal-header .modal-title").html('Edit Category');
    //Add hidden field for subadmin ID
    var input_hidden = '<input type="hidden" name="hidden_category_id" value=' + category_id + ' class="hidden_category_id">';
    $(".category_name").after(input_hidden);
    $("#status option[value='" + category_status + "']").attr("selected", "selected");
    $("#parent_id option[value='" + parent_category + "']").attr("selected", "selected");
    $('.category_name').val(category_name);
    $('.category_order').val(category_order);
    $('.show_cat_icon').attr('src', category_icon_path + '/' + category_icon);
    
    // var input_hidden1 = category_banner ? '<img src="' + category_banner_path + '/' + category_banner + '" alt="" width="100px" height="80px">' : '';
    // $(".banner_image").after(input_hidden1);
    
     // Hide or show banner image input based on parent category
     if (parent_category !== '') {
        $('.banner_images').hide(); // Hide banner image input if parent_category is blank
    } else {
        $('.banner_images').show(); // Show banner image input if parent_category is not blank
        var input_hidden1 = category_banner ? '<img src="' + category_banner_path + '/' + category_banner + '" alt="" width="100px" height="80px">' : '';
        $(".banner_image").after(input_hidden1);
    }

    $('#modal-default').on('hidden.bs.modal', function (e) {
        e.preventDefault();
        location.reload();
    })

});


/////////Delete Confirmation///////////
$('.show_confirm').click(function (event) {
    var form = $(this).closest("form");
    var name = $(this).data("name");
    event.preventDefault();
    swal({
        title: `Are you sure you want to delete this record?`,
        text: "If you delete this, it will be gone forever.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                form.submit();
            }
        });
});

////////Show image befoe upload////////// 
$('.cat-file-input').change(function () {
    var curElement = $('.show_cat_icon');
    // console.log(curElement);
    var reader = new FileReader();

    reader.onload = function (e) {
        // get loaded data and render thumbnail.
        curElement.attr('src', e.target.result);
    };
    // read the image file as a data URL.
    reader.readAsDataURL(this.files[0]);
});


// $(document).ready(function() {
//     const dash_nav_links = $('.nav-sidebar').find('.dash_nav_links')
//     dash_nav_links.on('click', function(e) {
//         e.preventDefault();
//         if($('.dash_nav_links').hasClass('active_links')){
//             dash_nav_links.removeClass('active_links');
//         }else{
//             dash_nav_links.addClass('active_links');
//         }
//         // console.log('working')
//         // $('.dash_nav_links').not($dash_nav_links).removeClass('active_links ');
//         // $dash_nav_links.addClass('active_links');
//     });
// });

// $(document).ready(function() {
//     const $dash_nav_links = $('.nav-sidebar').find('.dash_nav_links')
//     $dash_nav_links.on('click', function(e) {
//         console.log('ghhbnb')
//         e.preventDefault();
//         if($dash_nav_links.hasClass('active_links')){
//             $('.dash_nav_links').removeClass('active_links');
//             $dash_nav_links.addClass('active_links');
//         }
//     });
// });
