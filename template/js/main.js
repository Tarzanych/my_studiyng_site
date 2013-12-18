function createLangConstant() {
  $('.createLangConstantBlock').dialog({width: '500px'});
}

function t(attr) {
  var ret = '';
  if (attr.charAt(0) == '[' && attr.charAt(attr.length - 1) == ']') {
    if (typeof languageConstants[language][attr.substring(1, (attr.length - 2))] != 'undefined') {
      ret = languageConstants[language][attr.substring(1, (attr.length - 2))];
    }
  } else {
    for (i in languageConstants[languageDefault]) {
      if (languageConstants[languageDefault][i] == attr) {
        var v = i;
        if (typeof languageConstants[language][v] != 'undefined') {
          ret = languageConstants[language][v];
        }
      }
    }
  }
  if (ret == '') {
    ret = attr;
  }
  return ret;

}
function openCreateForm() {

  $('.createContentForm').dialog({width: '840px', open: function(event, ui) {
      $('.createContentForm').find('textarea').tinymce({
        script_url: '/template/js/tinymce/js/tinymce/tinymce.min.js',
        theme: 'modern',
        menubar: 'edit view format'
      });
      $('.createContentForm input[name=\'language\']:first').click();
    }});
}
function validateEmail(mail)
{
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
  {
    return true;
  }

  return false;
}
var combinations = [];
combinations.push({
  'name': 'User',
  'permissions': {
    'admin': 0,
    'create': 0,
    'edit': 0,
    'editOwn': 0,
    'del': 0,
    'delOwn': 0,
    'publish': 0,
    'publishOwn': 0
  }
});
combinations.push({
  'name': 'Editor',
  'permissions': {
    'admin': 0,
    'create': 1,
    'edit': 0,
    'editOwn': 1,
    'del': 0,
    'delOwn': 1,
    'publish': 0,
    'publishOwn': 1
  }
});
combinations.push({
  'name': 'Administrator',
  'permissions': {
    'admin': 1,
    'create': 1,
    'edit': 1,
    'editOwn': 1,
    'del': 1,
    'delOwn': 1,
    'publish': 1,
    'publishOwn': 1
  }
});
function changeLanguage(id) {
  if (parseInt(id) > 0) {
    $.post('/',
      {
        'Action': 'ChangeLanguage',
        'language': id
      }, function(data) {
      if (data.success) {
        window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname;
      } else {
        jAlert('Error', 'Close');
      }
    }, 'json');
  }
}
function editCategory(id) {
  $.post('/admin',
    {
      'Action': 'checkCategory',
      'catId': id
    }, function(data) {
    if (data.success) {
      $('.editCatBlock').each(function(n) {
        var el = $(this);
        el.find('input[name=\'catTitle\']').val(data.category.title);
        el.find('input[name=\'catUrl\']').val(data.category.url);
        el.find('input[name=\'catId\']').val(data.category.id);
        el.find('select[name=\'catParent\'] option').removeAttr('selected').removeAttr('disabled').each(function() {
          if ($(this).val() == data.category.id) {
            $(this).attr('disabled', 'disabled');
          }
          if ($(this).val() == data.category.parent) {
            $(this).attr('selected', 'selected');
          }
        });
        el.dialog({width: '840px', open: function(event, ui) {
            el.find('textarea').val(data.category.description).tinymce({
              script_url: '/template/js/tinymce/js/tinymce/tinymce.min.js',
              theme: 'modern',
              menubar: 'edit view format'
            });
          }});

      });
    } else {
      jAlert('Query error', 'Close');
    }
  }, 'json'
    );
}
function editLanguageVar(langVar) {

  $.post('/admin',
    {
      'Action': 'checkLangConstant',
      'langVar': langVar
    }, function(data) {
    if (data.success) {
      $('.editLangConstantBlock').each(function(n) {
        var el = $(this);
        el.find('input[name=\'constantTitle\']').val(data.langVar).focus();
        for (i in data.langVals) {
          el.find('input[name=\'constantVal[' + i + ']\']').val(data.langVals[i]);
        }
        el.dialog({width: '500px'});
      });
    } else {
      jAlert('Query error', 'Close');
    }
  }, 'json'
    );
}

function editContent(id) {

  $.post('/admin',
    {
      'Action': 'checkContent',
      'contentId': id
    }, function(data) {
    if (data.success) {
      $('.editContentBlock').each(function(n) {
        var el = $(this);

        el.find('input[name=\'contentUrl\']').val(data.content.url);
        el.find('input[name=\'contentId\']').val(data.content.id);
        for (i in data.content.preText) {
          el.find('input[name=\'contentTitle[' + i + ']\']').val(data.content.title[i]);
          el.find('textarea[name=\'contentPreText[' + i + ']\']').val(data.content.preText[i]);
          el.find('textarea[name=\'contentMainText[' + i + ']\']').val(data.content.totalText[i]);
        }
        $('.editContentForm input[name=\'language\']:first').click();
        el.find('select[name=\'contentCategory\'] option').removeAttr('selected').each(function() {
          if ($(this).val() == data.content.category_id) {
            $(this).attr('selected', 'selected');
          }
        });
        el.find('input[name=\'contentPublish\']').removeAttr('checked').each(function() {
          if ($(this).val() == data.content.publish) {
            $(this).attr('checked', 'checked');
          }
        });
        if (data.publish) {
          el.find('.publishBlock').show();
        } else {
          el.find('.publishBlock').hide();
        }
        el.find('input[name=\'contentFront\']').removeAttr('checked').each(function() {
          if ($(this).val() == data.content.onFront) {
            $(this).attr('checked', 'checked');
          }
        });
        el.dialog({width: '940px', open: function(event, ui) {
            el.find('textarea').tinymce({
              script_url: '/template/js/tinymce/js/tinymce/tinymce.min.js',
              theme: 'modern',
              plugins: 'image link lists charmap textcolor table visualblocks past media contextmenu'
            });
          }});

      });
    } else {
      jAlert('Query error', 'Close');
    }
  }, 'json'
    );
}


function deleteCategory(id) {
  $.post('/admin',
    {
      'Action': 'deleteCategory',
      'catId': id
    }, function(data) {
    if (data.success) {
      window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname;
    } else {
      jAlert(data.errors, 'Close');
    }
  }, 'json'
    );
}
function deleteContent(id) {
  if ($('.adminTable').size() > 0) {
    var admin = true;
  } else {
    var admin = false;
  }
  $.post('/admin',
    {
      'Action': 'deleteContent',
      'contentId': id,
    }, function(data) {
    if (data.success) {
      if (!admin) {
        window.location.href = data.url;
      } else {
        window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname;
      }

    } else {
      jAlert(data.errors, 'Close');
    }
  }, 'json'
    );
}
function deleteLanguageVar(langVar) {
  $.post('/admin',
    {
      'Action': 'deleteLanguageVar',
      'langVar': langVar
    }, function(data) {
    if (data.success) {
      window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname;

    } else {
      jAlert(data.errors, 'Close');
    }
  }, 'json'
    );
}
function getPermissionName() {
  var perm = null;
  var custom = true;
  var name = 'Custom';
  var admin = $('#admin').attr('data-value');
  var create = $('#create').attr('data-value');
  var edit = $('#edit').attr('data-value');
  var editOwn = $('#editOwn').attr('data-value');
  var del = $('#delete').attr('data-value');
  var delOwn = $('#deleteOwn').attr('data-value');
  var publish = $('#publish').attr('data-value');
  var publishOwn = $('#publishOwn').attr('data-value');
  for (i in combinations) {
    perm = combinations[i].permissions;
    if (perm.admin == admin && perm.create == create && perm.edit == edit && perm.editOwn == editOwn && perm.del == del && perm.delOwn == delOwn && perm.publish == publish && perm.publishOwn == publishOwn) {
      custom = false;
      name = combinations[i].name;

    }
  }

  $('#rule select option').removeAttr('selected');
  $('#rule select option[value=\'' + name + '\']').attr('selected', 'selected');
}
function setPermissionName(name, userId) {
  var ajaxObjects = '.permission';

  for (i in combinations) {
    if (name == combinations[i].name) {
// 			perm = combinations[i].permissions;
      $(ajaxObjects).html('<img src="/template/img/ajax-loader.gif" alt="loading" />');
      $.post('/admin', {
        'Action': 'changePermission',
        'userId': userId,
        'permission': name,
        'val': 1
      }, function(data) {
        if (data.success) {
          var perm = data.permissions;
          for (i in perm) {
            if (perm[i].permValue == 1) {
              var permText = '<span class="green">Yes</span>';
            } else {
              var permText = '<span class="red">No</span>';
            }
            $('#' + perm[i].permission).attr('data-value', perm[i].permValue).html(permText);
          }
        } else {
          jAlert('Permission update failed');
        }
      }, 'json');
    }
  }

  $('#rule select option').removeAttr('selected');
  $('#rule select option[value=\'' + name + '\']').attr('selected', 'selected');
}
$(function() {
  $('#rule select').append('<option value="Custom" disabled="disabled">Custom</option><option disabled="disabled">---------</option>');
  for (i in combinations) {
    $('#rule select').append('<option value="' + combinations[i].name + '">' + combinations[i].name + '</option>');
  }
  getPermissionName();
  if (!isRoot) {
    $('#rule select option[value=\'Administrator\']').attr('disabled', 'disabled');
  }
  $('#rule select').change(function() {
    var userId = $(this).closest('.profileBlock').attr('id').slice(7);
    setPermissionName($(this).val(), userId);
  });
  $('.searchBlock input[type=\'text\']').focus(function() {
    if ($(this).val() == searchText) {
      $(this).val('');
    }
  }).blur(function() {
    if ($(this).val() == '') {
      $(this).val(searchText);
    }
  });
  $('.menuBlock > ul > li').mouseenter(function() {
    if ($('ul', this).size() > 0) {
      $('> ul', this).show();

    }
  }).mouseleave(function() {
    $('ul', this).hide();
  });
  $('.catSelect').change(function() {
    window.location.href = "/admin/content/" + $(this).val();
  });
  $('.searchBlock form').submit(function() {
    if ($(this).find('input[type=\'text\']').val() == searchText) {
      jAlert('Enter search query, please', 'Close');
      return false;
    }
  });
  $('.login a:first').click(function() {
    $('.loginForm').toggle();
    if ($('.loginForm:visible').size() > 0) {
      $('.loginForm input:first').focus();
    }
  });
  $('.createLangConstantForm').submit(function() {
    if ($.trim($(this).find('input[name=\'constantTitle\']').val()) != '') {
      return true;
    } else {
      jAlert(t('Please enter variable name'));
      return false;
    }
  });
  $('.registerForm').submit(function() {
    var nick = $('.registerBlock input[name=\'nickname\']').val();
    var pass = $('.registerBlock input[name=\'password\']').val();
    var passconfirm = $('.registerBlock input[name=\'passconfirm\']').val();
    var email = $('.registerBlock input[name=\'email\']').val();
    var firstname = $('.registerBlock input[name=\'firstname\']').val();
    var secondname = $('.registerBlock input[name=\'secondname\']').val();
    var err = '';
    if (!nick || nick.length < 3 || nick.length > 25) {
      err += 'Invalid nickname length<br />';
    }
    if (!pass || !passconfirm || pass.length < 6 || pass.length > 15 || passconfirm.length < 6 || passconfirm.length > 15) {
      err += 'Invalid password length<br />';
    }
    if (pass != passconfirm) {
      err += 'Passwords doesn\'t match<br />';
    }
    if (!email || !validateEmail(email)) {
      err += 'Invalid email';
    }

    if (err.length > 0) {
      jAlert('<div class="bold">There are some errors:</div><br />' + err, 'Close');
      return false;
    } else {
      return true;
    }

  });
  $('.createCatForm').validate({submitHandler: function(form) {

      var title = $('.createCatForm input[name=\'catTitle\']').val();
      var url = $('.createCatForm input[name=\'catUrl\']').val();
      $.post('/admin/categories',
        {
          'Action': 'checkCreateCatForm',
          'title': title,
          'url': url
        }, function(data) {
        if (data.success) {
          form.submit();
          return false;
        } else {
          jAlert(data.errors, 'Close');
          return false;
        }
      }, 'json'
        );
    }});
  $('.createContentForm').validate({submitHandler: function(form) {

      var title = [];
      $('.createContentForm input[name^=\'contentTitle\']').each(function(n) {
        var name = $(this).attr('name');
        title[name.substring(13, name.length - 1)] = $(this).val();
      });
      var url = $('.createContentForm input[name=\'contentUrl\']').val();
      var category = $('.createContentForm input[name=\'contentCategory\']').val();
      var mainText = [];
      $('.createContentForm textarea[name^=\'contentMainText\']').each(function(n) {
        var name = $(this).attr('name');
        mainText[name.substring(16, name.length - 1)] = $(this).val();
      });

      $.post('/admin/content',
        {
          'Action': 'checkCreateContentForm',
          'title[]': title,
          'url': url,
          'category': category,
          'mainText[]': mainText
        }, function(data) {
        if (data.success) {
          form.submit();
          return false;
        } else {
          jAlert(data.errors, 'Close');
          return false;
        }
      }, 'json'
        );
    }});
  $('.createContentForm input[name=\'language\']').click(function() {
    var val = $(this).val();
    $('.createContentForm .languageText').hide();
    $('#create_language' + val + ', #create_title' + val).show();
  });
  $('.editContentForm input[name=\'language\']').click(function() {
    var val = $(this).val();
    $('.editContentForm .languageText').hide();
    $('#edit_language' + val + ",#edit_title" + val).show();
  });
  $('.editContentForm').validate({submitHandler: function(form) {

      var title = [];
      $('.editContentForm input[name^=\'contentTitle\']').each(function(n) {
        var name = $(this).attr('name');
        title[name.substring(13, name.length - 1)] = $(this).val();
      });
      var url = $('.editContentForm input[name=\'contentUrl\']').val();
      var category = $('.editContentForm input[name=\'contentCategory\']').val();
      var mainText = [];
      $('.editContentForm textarea[name^=\'contentMainText\']').each(function(n) {
        var name = $(this).attr('name');
        mainText[name.substring(16, name.length - 1)] = $(this).val();
      });

      var newAuthor = $('.editContentForm input[name=\'contentAuthor\']').val();
      var contentId = $('.editContentForm input[name=\'contentId\']').val();
      $.post('/admin/content',
        {
          'Action': 'checkEditContentForm',
          'title[]': title,
          'url': url,
          'category': category,
          'mainText[]': mainText,
          'newAuthor': newAuthor,
          'contentId': contentId
        }, function(data) {

        if (data.success) {
          form.submit();
          return false;
        } else {
          jAlert(data.errors, 'Close');
          return false;
        }
      }, 'json'
        );
    }});
  $('.editCatForm').validate({submitHandler: function(form) {

      var title = $('.editCatForm input[name=\'catTitle\']').val();
      var url = $('.editCatForm input[name=\'catUrl\']').val();
      var catId = $('.editCatForm input[name=\'catId\']').val();
      var catParent = $('.editCatForm input[name=\'catParent\']').val();
      var catAuthor = $('.editCatForm input[name=\'catAuthor\']').val();
      $.post('/admin/categories',
        {
          'Action': 'checkEditCatForm',
          'title': title,
          'url': url,
          'catId': catId,
          'catParent': catParent,
          'catAuthor': catAuthor
        }, function(data) {
        if (data.success) {
          form.submit();
          return false;
        } else {
          jAlert(data.errors, 'Close');
          return false;
        }
      }, 'json'
        );
    }});
  var maxHeight = 0;
  $('.registerBlock td').each(function() {
    if ($(this).outerHeight() > maxHeight) {
      maxHeight = $(this).outerHeight();
    }
  }).css('height', maxHeight);
  $('.loginForm form').submit(function() {
    var login = $(this).find("input[name='login']").val();
    var password = $(this).find("input[name='password']").val();

    $.post('/login',
      {
        'Action': 'login',
        'login': login,
        'password': password
      }, function(data) {

      if (data.success) {

        window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname;
      } else {
        jAlert(data.errors);
      }
    }, 'json'
      );
    return false;
  });
  $('.permission').click(function() {
    var ajaxObjects = '';
    var userId = $(this).closest('.profileBlock').attr('id').slice(7);
    var valid = true;
    var permission = $(this).attr('id');
    switch ($(this).attr('id')) {
      case 'admin':
        if ($(this).attr('data-value') == 0) {
          ajaxObjects = '.permission';
        } else {
          ajaxObjects = '#admin';
        }
        break;
      case 'create':
        ajaxObjects = '#create';
        break;
      case 'editOwn':
        ajaxObjects = '#editOwn';
        break;
      case 'edit':
        if ($(this).attr('data-value') == 1) {
          ajaxObjects = '#edit';
        } else {
          ajaxObjects = '#editOwn, #edit';
        }
        break;
      case 'deleteOwn':
        ajaxObjects = '#deleteOwn';
        break;
      case 'delete':
        if ($(this).attr('data-value') == 1) {
          ajaxObjects = '#delete';
        } else {
          ajaxObjects = '#deleteOwn, #delete';
        }
        break;
      case 'publishOwn':
        ajaxObjects = '#publishOwn';
        break;
      case 'publish':
        if ($(this).attr('data-value') == 1) {
          ajaxObjects = '#publish';
        } else {
          ajaxObjects = '#publishOwn, #publish';
        }
        break;
      default:
        valid = false;
        break;
    }
    if (valid) {
      if ($(this).attr('data-value') == 1) {
        var val = 0;
      } else {
        var val = 1;
      }
      $(ajaxObjects).html('<img src="/template/img/ajax-loader.gif" alt="loading" />');

      $.post('/admin', {
        'Action': 'changePermission',
        'permission': permission,
        'userId': userId,
        'val': val
      }, function(data) {
        if (data.success) {
          var perm = data.permissions;
          for (i in perm) {
            if (perm[i].permValue == 1) {
              var permText = '<span class="green">Yes</span>';
            } else {
              var permText = '<span class="red">No</span>';
            }
            $('#' + perm[i].permission).attr('data-value', perm[i].permValue).html(permText);
            getPermissionName()
          }
        } else {
          jAlert('Permission update failed');
        }
      }, 'json');

    }
  });
});