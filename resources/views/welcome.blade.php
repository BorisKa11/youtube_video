<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Тест выборки видео пользоавтеля YouTube</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: Verdana, sans-serif;
                font-weight: 100;
                margin: 0;
            }
			.page {margin:10px auto;width:900px;}
            .position-ref {
                position: relative;
            }

            .title {
                font-size: 30px;text-align:center;
            }

            .form {max-width:480px;text-align:center;margin:0px auto 20px;}
			.form input {color:#000;font-weight:600;}

            .m-b-md {
                margin-bottom: 30px;
            }
			table.videosTbl td {vertical-align:middle !important;text-align:left;}
			small {font-family:Helvetica;}
			@media and screen (max-width:900px) {
				.page {margin:10px auto;width:100%;}
			}
        </style>
    </head>
    <body>
        <div class="page">
            <div class="content">
                <div class="title m-b-md">
                    Поиск видео пользователя
                </div>

                <div class="form">
                    <form action="/" method="get" id="frmSearch">
						{{ csrf_field() }}
						<div class="input-group">
							<span class="hidden-xs input-group-addon">Имя пользователя:</span>						
							<input type="text" class="form-control" name="q" value="{{isset($q)?$q:''}}" placeholder="PushnoyRU">
							<span class="input-group-btn">
								<button class="btn btn-success" type="submit"><i class="glyphicon glyphicon-search"></i> Найти</button>
							</span>
						</div>
					</form>
                </div>
				<div class="videoList">
					
				</div>
            </div>
        </div>
		<script src="//code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script type="text/javascript">
			$('#frmSearch').on('submit',function(e) {
				e.preventDefault();
				getVideos();
			});
			function getVideos(n = '', p = '') {
				$.ajax({
                    type: "POST",
                    url: "/getVideos",
                    dataType: 'JSON',
                    data: {'q':$('input[name="q"]').val(),'n':n,'p':p},
                    beforeSend: function(request) {
                        return request.setRequestHeader('X-CSRF-Token', $("input[name='_token']").val());
                    },
                    success: function(data) {
                        if(data.status == 1) {
                            var t = [];
							t.push('<table class="table table-stripped videosTbl">');
							console.log(data.info)
							for(var i in data.videos) {
								var v = data.videos[i];
								t.push('<tr>');
								t.push('<td><img class="img-rounded" src="'+v.snippet.thumbnails.default.url+'" /></td>');
								t.push('<td>'+v.snippet.title);
								t.push(' <a href="https://www.youtube.com/watch?v='+v.id.videoId+'" target="_blank" data-toggle="tooltip" data-placement="bottom" data-original-title="Перейти к видео"><i class="glyphicon glyphicon-share"></i></a>');
								t.push('<div class="text-muted"><small>опубликовано '+formatDate(v.snippet.publishedAt)+'</small></div>');
								t.push('</td></tr>');
							}
							t.push('</table>');
							t.push('<div class="row">');
							if(data.info && data.info.prevPageToken && data.info.prevPageToken!='') {
								t.push('<div class="col-xs-4 text-left">');
								t.push('<a href="javascript:;" class="pull-left btn btn-success" onClick="getVideos(\'\',\''+data.info.prevPageToken+'\');">Назад</a>');
								t.push('</div>');
							} else {
								t.push('<div class="col-xs-4 text-left"></div>');
							}
							t.push('<div class="col-xs-4 text-center"><small>всего видео: '+data.info.totalResults+'</small></div>');
							if(data.info && data.info.nextPageToken && data.info.nextPageToken!='') {
								t.push('<div class="col-xs-4 text-right">');
								t.push('<a href="javascript:;" class="pull-right btn btn-success" onClick="getVideos(\''+data.info.nextPageToken+'\');">Дальше</a>');
								t.push('</div>');
							}
							t.push('</div>')
							$('.videoList:first').html(t.join(''));
							$('[data-toggle="tooltip"]').tooltip();
							$('body,html').animate({scrollTop:'0px'},200);							
                        } else {
                            $('.videoList:first').html('<div class="well">Нет видео для отображения</div>');
                        }
						// $('.videoList:first').html(data.ret);
                    }
                });
			}
			function formatDate(d) {
				var date = new Date(d);
				var dd = date.getDate();
				if (dd < 10) dd = '0' + dd;
				var mm = date.getMonth() + 1;
				if (mm < 10) mm = '0' + mm;
				var yy = date.getFullYear();
				if (yy < 10) yy = '0' + yy;
				var hh = date.getHours();
				if (hh < 10) hh = '0' + hh;
				var mi = date.getMinutes() + 1;
				if (mi < 10) mi = '0' + mi;
				return dd + '.' + mm + '.' + yy + ' в '+hh+':'+mi;
			}
		</script>
    </body>
</html>