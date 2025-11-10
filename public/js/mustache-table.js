//$(document).ready(function(){
    var pages = [1,2,3,4,5];
    var page_limit = 10;
    var init_load = true;
    var init_filter = false;

    let checkboxValue = JSON.parse(localStorage.getItem('checkbox_value')) || {};
    let isCheckAll = JSON.parse(localStorage.getItem('check_all')) || {};
    let selectedCheckbox = document.getElementsByClassName("custom-checkbox");
    let updateNotif = JSON.parse(localStorage.getItem('update_notif')) || {};

    if (typeof INIT_FILTER !== 'undefined' && INIT_FILTER) {
        init_filter = INIT_FILTER;
    }
    gblLoadTable()

    window.sessionStorage.removeItem("filter_value")
    window.sessionStorage.setItem("filter_value", '')

    function gblLoadTable(page = 1) {
        var filter, api_param = '';

        if (init_load) {
            window.sessionStorage.removeItem("filter_value")
            window.sessionStorage.setItem("filter_value", '')  
            init_load = false
        }

        if (window.sessionStorage.getItem("filter_value") != '') {
    	   filter = JSON.parse(window.sessionStorage.getItem("filter_value"))
        }

    	  if (filter != null && filter != '') {    		
    		  for (var [key, value] of Object.entries(filter)) {
	        	if (value != '') {
	          		api_param += '&filter[' + key + ']=' + encodeURIComponent(value)
	        	}
	    	  }
    	  }

        $.ajax({
            url : MUSTACHE_TABLE_API + '?page=' + page + api_param,
            beforeSend : function(){
                $('.loader').removeClass('d-none')
            },
            success: function(data){
                loadMustache(data);
                if(init_filter){
                    init_filter = false;
                    gblFilter();
                }
            }
        });
    }

    function loadMustache(data) {
        var page_count = 5;       
        var items = data.data
        
        var inFirst = false;
        var inLast = false;

        var headers = MUSTACHE_TABLE_HEADERS;

        if (data.last_page < page_count) {
            pages = [];
            for (i = data.current_page; i < parseInt(data.last_page); i++) {
                pages.push(i);
            }
        } else  if (pages.indexOf(data.current_page) < 0 || pages.indexOf(data.current_page) == 4) {
            if (data.current_page < data.last_page) {
                pages = [];
                for (i = data.current_page; i < (page_count + parseInt(data.current_page)); i++) {
                    if (i <= data.last_page) {
                        pages.push(i);
                    }
                }
            }    
        }

        if (data.current_page == data.last_page && data.last_page > page_count) {
            pages = [];
            for (i = data.last_page; i > (data.last_page - page_count); i--) {
                pages.push(i);
            }
            pages.reverse();
        }

        if (data.current_page < page_count) {
            inFirst = true
        }

        if (data.current_page > (parseInt(data.last_page) - page_count) && pages.indexOf(data.last_page) >= 0) {
            inLast = true
        }

        var page = {
            current : data.current_page,
            pages: pages,
            to : data.last_page,
            page_to : data.to,
            total : data.total,
            in_first : inFirst,
            in_last : inLast
        }

        var content = {
            headers,
            items,
            page
        }

        var template = $('#mustache-table').html();
        var templateScript = Handlebars.compile(template);
        var html = templateScript(content);
        
        $('#ajax-table').html(html);

        template = $('#mustache-table-mobile').html();
        if(template){
            templateScript = Handlebars.compile(template);
            html = templateScript(content);
            $('#ajax-table-mobile').html(html);
        }

        $('a.page-link').on('click', function() {
            var page = $(this).data('page')
            window.sessionStorage.setItem("current_page", JSON.stringify(page));
            gblLoadTable(page)
        })

        $('.loader').addClass('d-none')

        $.each(checkboxValue, function(key, value) {
          if (value) {
            $("#" + key).prop('checked', value);
          }
        });

        $("#toggleAllCheckbox").prop('checked', isCheckAll["check_all"]);
    }

    function toggleAllCheckbox(source) {
      wire_transfer_id = document.getElementsByName('wire_transfer_id');
      
      for(let i=0, n=wire_transfer_id.length;i<n;i++) {
        wire_transfer_id[i].checked = source.checked;
        checkboxValue[selectedCheckbox[i].id] = selectedCheckbox[i].checked;
      }

      if(!source.checked){
        for (const key in checkboxValue) {
          checkboxValue[key] = false;
        }
      }
  
      isCheckAll["check_all"] = source.checked;
    }

    function individualCheck() {
      for (let i = 0, n = selectedCheckbox.length; i < n; i++) {
          if (selectedCheckbox[i].checked) {
              checkboxValue[selectedCheckbox[i].id] = selectedCheckbox[i].checked;
          } else {
              checkboxValue[selectedCheckbox[i].id] = false;
          }
      }
      localStorage.setItem("checkbox_value", JSON.stringify(checkboxValue));
    }

    function gblExport(api, filename, element, type = 'blob') {
      var filter, qry_string = '';

      if (window.sessionStorage.getItem("filter_value") != '') {
       filter = JSON.parse(window.sessionStorage.getItem("filter_value"))
      }

      if (filter != null && filter != '') {       
        for (var [key, value] of Object.entries(filter)) {
          if (value != '') {
              if (qry_string == '') {
                qry_string += '?filter[' + key + ']=' + encodeURIComponent(value)
              } else {
                qry_string += '&filter[' + key + ']=' + encodeURIComponent(value)
              }
          }
        }
      }

      qry_string = api + qry_string
      
    	$.ajax({
            url : qry_string,
            xhrFields:{
              responseType: type
            },
            beforeSend : function(){
                element.attr('disabled', true);
                $("#preloader").css("display","block");
            },
            success: function(data, status ,xhr){
              let BOM = new Uint8Array([0xEF,0xBB,0xBF]);
              let blob = (type == 'text') ? new Blob([BOM, data], {encoding:'UTF-8', type: 'application/vnd.ms-excel; charset=UTF-8'})
                : new Blob([data], {type: 'application/vnd.ms-excel'});
		          var url = window.URL || window.webkitURL;
              link = url.createObjectURL(blob);
              var a = $("<a />");
              a.attr("download", filename);
              a.attr("href", link);
              $("body").append(a);
              a[0].click();
              $("body").remove(a);
              element.attr('disabled', false);
              $("#preloader").css("display","none");
            }
        });  
    }
    
    function gblFilter() {
        var payload = {};

       $.each($('.filter input'), function(index, item){
         if($(item).attr('type') == "checkbox") {
           if ($(item).is(":checked")) {
            payload[$(item).attr('name')] = typeof payload[$(item).attr('name')] !== "undefined" ? payload[$(item).attr('name')] + "," + $(item).val() : $(item).val();
           }
         } else {
            if ($(item).val() != '') {
              if ($(item).val() != '') {
                var fieldname = (($(item).attr('id') == undefined)) ? $(item).attr('name') : $(item).attr('id');
                payload[fieldname] = $(item).val();
              }
            }
         }
       })

       $.each($('.filter select'), function(index, item){
        if ($(item).val() != '') {
            payload[$(item).attr('id')] = $(item).val();
        }
       })

       if ($('#search').length) {
            payload['search'] = $('#search').val()
       }

      if ($('#rows').val() != '10') {
        payload['rows'] = $('#rows').val()
      }

       window.sessionStorage.setItem("filter_value", JSON.stringify(payload));
       gblLoadTable()
    }
//});

Handlebars.registerHelper('ifCond', function(v1, v2, options) {
  if(v1 === v2) {
    return options.fn(this);
  }
  return options.inverse(this);
});

Handlebars.registerHelper('hSubtract', function(val1, val2) {
  return (val1 - val2);
});

Handlebars.registerHelper('hAdd', function(val1, val2) {
  return (val1 + val2);
});

Handlebars.registerHelper('hCurrent', function(page) {
  var page = (page - 1) * page_limit;

  if (page == 0) {
    return 1;
  }

  return (page + 1) 
});

Handlebars.registerHelper('hReplace', function(subject, needle, replace) {
  return subject ? subject.replace(needle, replace) : subject
});

Handlebars.registerHelper('hFormatDate', function(date, time = false) {
      let tempDate = date.replace(' ', 'T')
      let formatDate = new Date(tempDate)
      let meredian = ' AM'
      let day = formatDate.getDate()
      let month = formatDate.getMonth() + 1;

      if (day  < 10) {
        day = '0' + String(day);
      }

      if (month  < 10) {
        month = '0' + String(month);
      }

      let returnDate = formatDate.getFullYear() + '-' + month + '-' +  day   

      let hour = formatDate.getHours()
      let minutes = formatDate.getMinutes()
      let seconds = formatDate.getSeconds()
      if (hour >= 12) {
        meredian = ' PM'
        if (hour > 12) {
          hour = hour - 12
        }
      }

      if (hour < 10) {

        hour = hour == 0 ? 12 : '0' + hour
      }

      if (minutes < 10) {
        minutes = '0' + minutes
      }

      if (seconds < 10) {
        seconds = '0' + seconds
      }

      if (time) {
        returnDate =  returnDate + ' ' + hour + ':' + minutes + ':' + seconds + meredian
      }
      return returnDate
});

Handlebars.registerHelper('hStatusClass', function(status) {

    var translatedStatus = '';
    if (typeof TRANSLATED_STATUS !== 'undefined') {
        translatedStatus = TRANSLATED_STATUS;
    }

  var tClass = '';

  if (status == null) {
    return '';
  }

  status = status.toLowerCase();

  if (status == 'applying' || status == 'dormant') {
    tClass = 'badge-warning';
  } else if (status == 'completed' || status == 'verified') {
    tClass = 'badge-success';
  } else if (status == 'reject' || status == 'canceled' || status == 'unverified' || status == 'suspended') {
    tClass = 'badge-danger';
  }

  status =  status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });

  status = typeof translatedStatus[status] !== 'undefined' ? translatedStatus[status] : status;
  return new Handlebars.SafeString('<span class="badge ' + tClass + '">' + status + '</span>');

});

Handlebars.registerHelper('hFormatNumber', function(amount) {
  if (amount != undefined) {
    return amount.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
  }

  return '';
});

Handlebars.registerHelper('hStrReplace', function(string,search,replace) {
    if (string != undefined) {
        return string.replaceAll(search, replace);
    }
    return '';
});

$(document).ready(function(){
    // clear checked checkbox value on page reload
    localStorage.removeItem("checkbox_value");
    for (const key in checkboxValue) {
      checkboxValue[key] = false;
    }

    $('#btn-search').on('click', function(){
        gblFilter();
    });
    $('.table-container #search').on("keypress", function(e) {
        if (e.keyCode == 13) {
            gblFilter();
            
        }
    });

    $('#filter').on("click", function() {
        gblFilter();

        checkboxValue = {};
        isCheckAll = {};
    });

    $('#rows').on("change", function() {
      gblFilter();
    });

    $('#reset').on("click", function() {
        $.each($('.filter input'), function(index, item){
            if($(item).attr('type') == "checkbox") {
              $(item).prop('checked', false);;
            } else {
              $(item).val('');
            }
       })

       $.each($('.filter select'), function(index, item){
        if($(item).attr('id') == 'search_type') {
          $(item).prop("selectedIndex", 0); 
        } else {
            $(item).val('');
        }
       })

       $('#search').val('');
       $('#rows').prop('selectedIndex',0);

       window.sessionStorage.removeItem("filter_value")
       window.sessionStorage.getItem("filter_value", '')

       gblLoadTable()
       
      checkboxValue = {};
      isCheckAll = {};
    });

    $('#search').on('click', function(){
      $.each($('.filter-dashboard input'), function(index, item){
        if($(item).attr('type') == "checkbox") {
          $(item).prop('checked', false);;
        } else {
          $(item).val('');
        }
      })

      $.each($('.filter-dashboard select'), function(index, item){
        $(item).val('');
      })
    });

    $('#access_type, #account_status, #account_type').on('click', function(){
        $('#search').val('');
    });

    $.each($('.filter-dashboard input'), function(index, item){
      $(item).on('click', function(){
        $('#search').val('');
      });
    });

    if(updateNotif['result']=='success') {
      toastr.success(updateNotif['message']);
    } else if (updateNotif['result']=='error') {
      toastr.error(updateNotif['message']);
    }

    localStorage.removeItem("update_notif");
    for (const key in updateNotif) {
      updateNotif[key] = "";
    }

})