global_res = null;

jQuery(document).ready(function ($) {

  $('#submit-query-button').click(function(e){

    if($('#submit-query-button').hasClass("disabled")) {
      return;
    }
    var query = ace.edit("editor").getValue();

    submit_query(query, false);

    //updateQuerylogSize();
  });

  $('#joyride-button').click(function(e){
    $(document).foundation('joyride', 'start');
    $('.menu-content').hide();
    $('.menu-bg').hide();
  });

  $('#continue_button').click(function(e){
    if ($('#continue_button').hasClass('disabled')) { return; }
    if(global_res == null) {
      ajaxdata("query.php?query=continue", query_callback, null, null, null);
    } else {
      if(global_res.solved != undefined) {
        $('#continue_button').show();
        $('#submit-query-button').addClass("disabled");
      } else {
        $('#continue_button').hide();
        $('#submit-query-button').removeClass("disabled");
      }
      $('#exercise_text').text(global_res.description2);
      if(global_res.speaker2 != undefined) {
        if(global_res.speaker2 == "R") {
          $('.text-box-pointer').css('left', 190);
          $('.text-box-pointer-shadow').css('left', 183);
        } else {
          $('.text-box-pointer').css('left', 20);
          $('.text-box-pointer-shadow').css('left', 13);
        }
      }
      if(global_res.query != undefined) {
        submit_query(global_res.query, true);
      }
      global_res = null;
    }
  });

  $('#really-restart-button').click(function(e){
    $('#querylog').text('');
    $('#restart-modal').foundation('reveal', 'close');
    $('.menu-content').hide();
    $('.menu-bg').hide();
    ajaxdata("query.php?query=restart", query_callback, null, null, null);
  });

  $('#not-restart-button').click(function(e){
    $('#restart-modal').foundation('reveal', 'close');
    $('.menu-content').hide();
    $('.menu-bg').hide();
  });
});

function submit_query(query, typewriter) {
  if(query=="") { return; }

  Ladda.create( document.querySelector( '#submit-query-button' ) ).start();

  //$('#querylog').append('<pre><code class="sql">'+query+'</code></pre>');
  $('#querylog').append('<pre><code class="sql">'+(typewriter?'':query)+'</code></pre>');
  updateQuerylogSize();
  elem = document.getElementById('querylog').childNodes[document.getElementById('querylog').childNodes.length-1].childNodes[0];
  hljs.highlightBlock(elem);

  if(typewriter) {
    $('#continue_button').addClass("disabled");
    var i = 0, text;
    (function type() {
        text = query.slice(0, ++i);
        elem.innerHTML = text;
        hljs.highlightBlock(elem);
        updateQuerylogSize();

        if (text === query) {
          ajaxdata("query.php?query="+encodeURIComponent(umlaute(query)), query_callback, null, null, null);
          return;
        }
        setTimeout(type, 80);
    }());
  } else {
    ajaxdata("query.php?query="+encodeURIComponent(umlaute(query)), query_callback, null, null, null);
  }


}


function query_callback(result, a, b, c) {
  Ladda.create( document.querySelector( '#submit-query-button' ) ).stop();
  $('#continue_button').removeClass("disabled");
  var res = JSON.parse(result);

  if(res.result != undefined) {
    if(res.code == undefined) {
      return;
    }
    if(res.result.length<1) {
      return;
    }
    var table = '<table border="1"><thead><tr>';
    for(var i = 0; i < res.result[0].length; i++) {
      table += '<th>'+res.result[0][i]+'</th>';
    }
    table += '</tr></thead>';

    for(var k = 1; k < res.result.length; k++) {
      table += '<tr>';
      for(var i = 0; i < res.result[k].length; i++) {
        table += '<td>'+res.result[k][i]+'</td>';
      }
      table += '</tr>';
    }
    table += '</table>';

    $('#querylog').append(table);
  }
  if(res.msg != undefined) {
    if(res.code < 0) { alertType = 'error'; }
    else if (res.code > 0) {
      alertType = 'success';

      if(res.answer != undefined) {
        $('#exercise_text').text(res.answer);
      }

      $('#continue_button').show();
      $('#submit-query-button').addClass("disabled");
    }
    else { alertType = 'warning'; }
    $('#querylog').append('<div data-alert class="alert-box '+alertType+' radius">'+res.msg+'</div>');
  }
  updateQuerylogSize();

  /* success, only show Continue button, no exercise and query */
  if(res.code != undefined && res.code > 0) {
    return;
  }

  if(res.exercise != undefined) {
     $('#exercise_text').text(res.exercise);
  }
  if(res.certificate != undefined) {
     $('#certificate_button').show();
  }

  if(res.speaker != undefined) {
    if(res.speaker == "R") {
      $('.text-box-pointer').css('left', 190);
      $('.text-box-pointer-shadow').css('left', 183);
    } else {
      $('.text-box-pointer').css('left', 20);
      $('.text-box-pointer-shadow').css('left', 13);
    }
  }

  if(res.leftimg != undefined && res.leftimg != "") {
    $('#leftimg').css('background-image', "url('./images/wf/"+res.leftimg+".png')");
  } else if(res.leftimg != undefined && res.leftimg == "") {
    $('#leftimg').css('background-image', '');
  }
  if(res.rightimg != undefined && res.rightimg != "") {
    $('#rightimg').css('background-image', "url('./images/wf/"+res.rightimg+".png')");
  } else if(res.rightimg != undefined && res.rightimg == "") {
    $('#rightimg').css('background-image', '');
  }

x=res
  if(res.solved != undefined) {
    $('#continue_button').show();
    $('#submit-query-button').addClass("disabled");
  } else {
    $('#continue_button').hide();
    $('#submit-query-button').removeClass("disabled");
  }

  if(res.description2 != undefined) {
     description2 = res.description2;
     $('#continue_button').show();
     $('#submit-query-button').addClass("disabled");
     global_res = res;
     return;
  }



  if(res.query != undefined) {
    submit_query(res.query, true);
  }
}


function updateQuerylogSize() {
  $height = document.documentElement.clientHeight,
  $('#querylog').css({'height': $height-400});
  $('#querylog').scrollTop($('#querylog').prop('scrollHeight'));
}

$(window)
    .load(function() {
        updateQuerylogSize();
    })
    .resize(function(){
        updateQuerylogSize();
    });


function ajaxdata(url, callback, arg1, arg2, arg3)
{

        var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

        request.open("GET", "./" + url, true);


        request.onreadystatechange = function()
	{
		if(request.readyState == 4 && (request.status == 200 || request.status == 302 || request.status == 301))
		{
			callback(request.responseText, arg1, arg2, arg3);
		}
        }
        request.send(null);
}

function umlaute(text) {
	//only replace part after where
	if(text.toUpperCase().indexOf('WHERE') == -1) { return text; }
	output1 = text.substr(0, text.toUpperCase().indexOf('WHERE'));
	output2 = text.substr(text.toUpperCase().indexOf('WHERE'));
	output2 = output2.replace(/ä/g,"ae").replace(/ö/g,"oe").replace(/ü/g,"ue").replace(/Ä/g,"Ae").replace(/Ö/g,"Oe").replace(/Ü/g,"Ue").replace(/ß/g,"ss");
	return output1+output2;
}
