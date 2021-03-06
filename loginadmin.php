<?php
   session_start();
   error_reporting(0);
   define('ADMIN_PASS', '08d55d6d107e94a8f7900741824d1fe8');
   $session_timeout = 6000;
   $database = './usersdb.php';
   $admin_password = isset($_COOKIE['admin_password']) ? $_COOKIE['admin_password'] : '';
   if (empty($admin_password))
   {
      if (isset($_POST['admin_password']))
      {
         $admin_password = md5($_POST['admin_password']);
         if ($admin_password == ADMIN_PASS)
         {
            setcookie('admin_password', $admin_password, time() + $session_timeout);
         }
      }
   }
   else
   if ($admin_password == ADMIN_PASS)
   {
      setcookie('admin_password', $admin_password, time() + $session_timeout);
   }
   if (!file_exists($database))
   {
      echo 'User database not found!';
      exit;
   }
   $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
   $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
   $index = 0;
   $userindex = -1;
   $items = file($database, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
   foreach($items as $line)
   {
      list($username) = explode('|', trim($line));
      if ($id == $username)
      {
         $userindex = $index;
      }
      $index++;
   }
   if (!empty($action))
   {
      if ($action == 'delete')
      {
         if ($userindex == -1)
         {
            echo 'User not found!';
            exit;
         }
         $file = fopen($database, 'w');
         $index = 0;
         foreach($items as $line)
         {
            if ($index != $userindex)
            {
               fwrite($file, $line);
               fwrite($file, "\r\n");
            }
            $index++;
         }
         fclose($file);
         header('Location: '.basename(__FILE__));
         exit;
      }
      else
      if ($action == 'update')
      {
         $file = fopen($database, 'w');
         $index = 0;
         foreach($items as $line)
         {
            if ($index == $userindex)
            {
               $values = explode('|', trim($line));
               $values[0] = $_POST['username'];
               if (!empty($_POST['password']))
               {
                  $values[1] = md5($_POST['password']);
               }
               $values[2] = $_POST['email'];
               $values[3] = $_POST['fullname'];
               $values[4] = $_POST['active'];
               $values[6] = $_POST['extra1'];
               $values[7] = $_POST['extra2'];
               $values[8] = $_POST['extra3'];
               $line = '';
               for ($i=0; $i < count($values); $i++)
               {
                  if ($i != 0)
                     $line .= '|';
                  $line .= $values[$i];
               }
            }
            fwrite($file, $line);
            fwrite($file, "\r\n");
            $index++;
         }
         fclose($file);
         header('Location: '.basename(__FILE__));
         exit;
      }
      else
      if ($action == 'create')
      {
         for ($i=0; $i < $index; $i++)
         {
            if ($usernames[$i] == $_POST['username'])
            {
               echo 'User already exists!';
               exit;
            }
         }
         $file = fopen($database, 'a');
         fwrite($file, $_POST['username']);
         fwrite($file, '|');
         fwrite($file, md5($_POST['password']));
         fwrite($file, '|');
         fwrite($file, $_POST['email']);
         fwrite($file, '|');
         fwrite($file, $_POST['fullname']);
         fwrite($file, '|');
         fwrite($file, $_POST['active']);
         fwrite($file, '|NA');
         fwrite($file, '|');
         fwrite($file, $_POST['extra1']);
         fwrite($file, '|');
         fwrite($file, $_POST['extra2']);
         fwrite($file, '|');
         fwrite($file, $_POST['extra3']);
         fwrite($file, "\r\n");
         fclose($file);
         header('Location: '.basename(__FILE__));
         exit;
      }
      else
      if ($action == 'logout')
      {
         session_unset();
         session_destroy();
         setcookie('admin_password', '', time() - 3600);
         header('Location: '.basename(__FILE__));
         exit;
      }
   }
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>User Administrator</title>
<style type="text/css">
* 
{
   box-sizing: border-box;
}
body
{
   background-color: #FFFFFF;
   margin: 6px;
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   color: #000000;
}
th
{
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   background-color: #337AB7;
   color: #FFFFFF;
   text-align: left;
}
td
{
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   color: #000000;
}
input, select
{
   font-size: 13px;
   font-family: Arial;
   font-weight: normal;
   text-decoration: none;
   color: #000000;
   border:1px #000000 solid;
}
.clickable
{
   cursor: pointer;
}
.container
{
   max-width: 768px;
   margin: 0 auto 0 auto;
   padding: 15px;
   text-align: left;
   width: 100%;
}
td, th 
{
   padding: 0;
}
.table 
{
   background-color: transparent;
   border: 1px solid #DDDDDD;
   border-collapse: collapse;
   border-spacing: 0;
   max-width: 100%;
   width: 100%;
}
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td 
{
   padding: 8px;
   line-height: 1.4285;
   vertical-align: top;
   border-top: 1px solid #DDDDDD;
}
.table > thead > tr > th 
{
   vertical-align: bottom;
   border-bottom: 2px solid #DDDDDD;
}
.table > caption + thead > tr:first-child > th, .table > colgroup + thead > tr:first-child > th, .table > thead:first-child > tr:first-child > th, .table > caption + thead > tr:first-child > td, .table > colgroup + thead > tr:first-child > td, .table > thead:first-child > tr:first-child > td 
{
   border-top: 0;
}
.table-hover > tbody > tr:hover > td
{
   background-color: #F5F5F5;
}
.table-striped>tbody>tr:nth-child(odd)>td
{
   background-color: #F9F9F9;
}
th
{
   background-color: #337AB7;
   color: #FFFFFF;
   font-weight: bold;
}
.form-control 
{
   display: block;
   width: 100%;
   margin-bottom: 15px;
   padding: 6px 12px;
   font-family: Arial;
   font-size: 13px;
   line-height: 1.4285;
   color: #555555;
   background-color: #FFFFFF;
   background-image: none;
   border: 1px solid #CCCCCC;
   border-radius: 4px;
   box-shadow: inset 0px 1px 1px rgba(0,0,0,0.075);
   -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
   transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}
.form-control:focus 
{
   border-color: #66AFE9;
   outline: 0;
   box-shadow: inset 0px 1px 1px rgba(0,0,0,0.075), 0px 0px 8px rgba(102,175,233,0.60);
}
label
{
   display: block;
   padding: 6px 0px;
   text-align: left;
}
.btn
{
   display: inline-block;
   padding: 6px 12px;
   margin-bottom: 0px;
   font-family: Arial;
   font-weight: normal;
   font-size: 13px;
   text-align: center;
   text-decoration: none;
   white-space: nowrap;
   vertical-align: middle;
   cursor: pointer;
   -webkit-user-select: none;
   -moz-user-select: none;
   -ms-user-select: none;
   user-select: none;
   background-color: #337AB7;
   border: 1px solid #2E6DA4;
   border-radius: 4px;
   color: #FFFFFF;
}
#header
{
   margin-bottom: 6px;
}
#filter
{
   float: right;
}
#filter input
{
   display: inline-block;
   vertical-align: middle;
   width: 16em;
   padding: 5px 10px;
}
#filter label
{
   display: inline-block;
   max-width: 100%;
   font-size: 13px;
   font-family: Arial;
}
.filter-hide
{
   display: none !important;
}
#pagination
{
   display: inline-block;
   list-style: none;
   padding: 0;
   border-radius: 4px;
   font-family: Arial;
   font-weight: normal;
   font-size: 0;
}
#pagination > li
{
   display: inline;
   font-size: 13px;
}
#pagination > li > a, #pagination > li > span
{
   position: relative;
   float: left;
   padding: 6px 12px 6px 12px;
   text-decoration: none;
   background-color: #FFFFFF;
   border: 1px #DDDDDD solid;
   color: #337AB7;
   margin-left: -1px;
}
#pagination > li:first-child > a, #pagination > li:first-child > span
{
   margin-left: 0;
   border-bottom-left-radius: 4px;
   border-top-left-radius: 4px;
}
#pagination > li:last-child > a, #pagination > li:last-child > span
{
   border-bottom-right-radius: 4px;
   border-top-right-radius: 4px;
}
#pagination > li > a:hover, #pagination > li > span:hover, #pagination > li > a:focus, #pagination > li > span:focus 
{
   background-color: #CCCCCC;
   color: #23527C;
}
#pagination > .active > a, #pagination > .active > span, #pagination > .active > a:hover, #pagination > .active > span:hover, #pagination > .active > a:focus, #pagination > .active > span:focus
{
   z-index: 2;
   background-color: #337AB7;
   border-color: #337AB7;
   color: #FFFFFF;
   cursor: default;
}
#pagination > .disabled > span, #pagination > .disabled > span:hover, #pagination > .disabled > span:focus, #pagination > .disabled > a, #pagination > .disabled > a:hover, #pagination > .disabled > a:focus 
{
   background-color: #FFFFFF;
   color: #777777;
   cursor: not-allowed;
}
.paginate-show
{
   display: table-row;
}
.paginate-hide
{
   display: none;
}
#footer
{
   margin-top: 10px;
   text-align:right;
}
.icon-edit, .icon-delete
{
   display: inline-block;
}
.icon-edit::before
{
   display: inline-block;
   width: 13px;
   height: 13px;
   content: " ";
   background: url('data:image/svg+xml,%3Csvg%20height%3D%2213%22%20width%3D%2213%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20style%3D%22fill%3A%23000000%22%20transform%3D%22scale%280.0073%29%22%3E%0D%0A%3Cpath%20transform%3D%22rotate%28180%29%20scale%28-1%2C1%29%20translate%280%2C-1536%29%22%20d%3D%22M363%200l91%2091l-235%20235l-91%20-91v-107h128v-128h107zM886%20928q0%2022%20-22%2022q-10%200%20-17%20-7l-542%20-542q-7%20-7%20-7%20-17q0%20-22%2022%20-22q10%200%2017%207l542%20542q7%207%207%2017zM832%201120l416%20-416l-832%20-832h-416v416zM1515%201024q0%20-53%20-37%20-90l-166%20-166l-416%20416l166%20165q36%2038%2090%2038q53%200%2091%20-38l235%20-234q37%20-39%2037%20-91z%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E') no-repeat center center;
}
.icon-delete::before
{
   display: inline-block;
   width: 13px;
   height: 13px;
   content: " ";
   background: url('data:image/svg+xml,%3Csvg%20height%3D%2213%22%20width%3D%2213%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20style%3D%22fill%3A%23000000%22%20transform%3D%22scale%280.0073%29%22%3E%0D%0A%3Cpath%20transform%3D%22rotate%28180%29%20scale%28-1%2C1%29%20translate%280%2C-1536%29%22%20d%3D%22M1298%20214q0%20-40%20-28%20-68l-136%20-136q-28%20-28%20-68%20-28t-68%2028l-294%20294l-294%20-294q-28%20-28%20-68%20-28t-68%2028l-136%20136q-28%2028%20-28%2068t28%2068l294%20294l-294%20294q-28%2028%20-28%2068t28%2068l136%20136q28%2028%2068%2028t68%20-28l294%20-294l294%20294q28%2028%2068%2028t68%20-28l136%20-136q28%20-28%2028%20-68t-28%20-68l-294%20-294l294%20-294q28%20-28%2028%20-68z%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E') no-repeat center center;
}
#avatar *
{
   box-sizing: border-box;
}
#avatar
{
   display: table;
   border-collapse: separate;
   border-spacing: 0;
   margin-bottom: 15px;
}
#avatar .form-control
{
   position: relative;
   z-index: 2;
   float: left;
   width: 100%;
   margin-bottom: 0px;
}
#avatar .input-group-btn, #avatar .form-control
{
   display: table-cell;
}
#avatar .form-control
{
   border-bottom-right-radius: 0px;
   border-top-right-radius: 0px;
   margin: 0;
}
#avatar .input-group-btn
{
   width: 1%;
   white-space: nowrap;
   vertical-align: middle;
   padding: 0;
   position: relative;
   font-size: 0px;
   white-space: nowrap;
}
#avatar .btn
{
   border-bottom-left-radius: 0px;
   border-top-left-radius: 0px;
   display: inline-block;
   margin-left: -1px;
   padding: 6px 8px 6px 8px;
   position: relative;
}
.thumbnail 
{
   display: inline-block;
   min-height: 1px;
   box-sizing: border-box;
   margin: 0;
   padding: 6px 0 14px 0;
   text-align: center;
   vertical-align: top;
}
.thumbnail .frame 
{
   padding: 4px;
   background-color: #fff;
   border: 1px #DDDDDD solid;
   border-radius: 4px;
}
.thumbnail img 
{
   border-width: 0;
   display: block;
   width: 100%;
   height: auto;
   max-width: 100%;
   box-sizing: border-box;
}
</style>
<script type="text/javascript" src="jquery-1.12.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
   $('#filter input').on('propertychange input', function(e)
   {
      $('.no-results').remove();
      var $this = $(this);
      var search = $this.val().toLowerCase();
      var $target = $('.table');
      var $rows = $target.find('tbody tr');
      if (search == '') 
      {
         $rows.removeClass('filter-hide');
         buildNav();
         paginate();
      } 
      else 
      {
         $rows.each(function()
         {
            var $this = $(this);
            $this.text().toLowerCase().indexOf(search) === -1 ? $this.addClass('filter-hide') : $this.removeClass('filter-hide');
         })
         buildNav();
         paginate();
         if ($target.find('tbody tr:visible').size() === 0) 
         {
            var col_span = $target.find('tr').first().find('td').size();
            var no_results = $('<tr class="no-results"><td colspan="'+col_span+'"></td></tr>');
            $target.find('tbody').append(no_results);
         }
      }
   });
   $('.table').each(function()
   {
      var currentPage = 0;
      var numPerPage = 10;
      var $table = $(this);
      var numRows = $table.find('tbody tr').length;
      var numPages = Math.ceil(numRows / numPerPage);
      var $pagination = $('#pagination');
      paginate = function()
      {
         $pagination.find('li').eq(currentPage+1).addClass('active').siblings().removeClass('active');
         var $prev = $pagination.find('li:first-child');
         var $next = $pagination.find('li:last-child');
         if (currentPage == 0)
         {
            $prev.addClass('disabled');
         }
         else
         {
            $prev.removeClass('disabled');
         }
         if (currentPage == (numPages-1))
         {
            $next.addClass('disabled');
         }
         else
         {
            $next.removeClass('disabled');
         }
         $table.find('tbody tr').not('.filter-hide').removeClass('paginate-show').addClass('paginate-hide').slice(currentPage * numPerPage, (currentPage + 1) * numPerPage).removeClass('paginate-hide').addClass('paginate-show');;
      };
      buildNav = function()
      {
         numRows = $table.find('tbody tr').not('.filter-hide').length;
         numPages = Math.ceil(numRows / numPerPage);
         $pagination.find('li').not($pagination.find('li:first-child')).not($pagination.find('li:last-child')).remove();
         for (var page = 0; page < numPages; page++)
         {
            var item = '<a>' + (page + 1) + '</a>';
            $('<li></li>').html(item)
            .bind('click', {newPage: page}, function(event)
            {
               currentPage = event.data['newPage'];
               paginate();
            }).appendTo($pagination).addClass('clickable');
         }
         $pagination.find('li').eq(1).appendTo($pagination);
      }
      buildNav();
      $pagination.find('li:nth-child(2)').addClass('active');
      $pagination.find('li:first-child').click(function()
      {
         if (currentPage > 0)
         {
            currentPage--;
         }
         paginate();
      });
      $pagination.find('li:last-child').click(function()
      {
         if (currentPage < (numPages-1))
         {
            currentPage++;
         }
         paginate();
      });
      paginate();
   });
   $("#avatar :file").on('change', function()
   {
      var input = $(this).parents('.input-group').find(':text');
      input.val($(this).val());
   });
});
</script>
</head>
<body>
<?php
   if ($admin_password != ADMIN_PASS)
   {
      echo "<div class=\"container\" style=\"text-align:center\">\n";
      echo "<h2>User Administrator</h2>\n";
      echo "<form method=\"post\" accept-charset=\"UTF-8\" action=\"" .basename(__FILE__) . "\">\n";
      echo "<input class=\"form-control\" type=\"password\" name=\"admin_password\" size=\"20\" />\n";
      echo "<input class=\"btn\" type=\"submit\" value=\"Login\" name=\"submit\" />\n";
      echo "</form>\n";
      echo "</div>\n";
   }
   else
   {
      if (!empty($action))
      {
         if (($action == 'edit') || ($action == 'new'))
         {
            if ($userindex != -1)
            {
               $values = explode('|', trim($items[$userindex]));
               $username_value = $values[0];
               $email_value = $values[2];
               $fullname_value = $values[3];
               $active_value = $values[4];
               $extra1_value = $values[6];
               $extra2_value = $values[7];
               $extra3_value = $values[8];
            }
            else
            {
               $username_value = "";
               $fullname_value = "";
               $email_value = "";
               $active_value = "0";
               $extra1_value = "";
               $extra2_value = "";
               $extra3_value = "";
            }
            echo "<div class=\"container\">\n";
            echo "<form action=\"" . basename(__FILE__) . "\" enctype=\"multipart/form-data\" accept-charset=\"UTF-8\" method=\"post\">\n";
            if ($action == 'new')
            {
               echo "<input name=\"action\" type=\"hidden\" value=\"create\">\n";
            }
            else
            {
               echo "<input name=\"action\" type=\"hidden\" value=\"update\">\n";
            }
            echo "<input type=\"hidden\" name=\"id\" value=\"". $id . "\">\n";
            echo "<label for=\"username\">Username</label>\n";
            echo "<input class=\"form-control\" id=\"username\" name=\"username\" size=\"50\" type=\"text\" value=\"" . $username_value . "\">\n";
            echo "<label for=\"password\">Password</label>\n";
            echo "<input class=\"form-control\" id=\"password\" name=\"password\" size=\"50\" type=\"password\" value=\"\">\n";
            echo "<label for=\"fullname\">Fullname</label>\n";
            echo "<input class=\"form-control\" id=\"fullname\" name=\"fullname\" size=\"50\" type=\"text\" value=\"" . $fullname_value . "\">\n";
            echo "<label for=\"email\">Email</label>\n";
            echo "<input class=\"form-control\" id=\"email\" name=\"email\" size=\"50\" type=\"text\" value=\"" . $email_value . "\">\n";
            echo "<label for=\"role\">Role</label>\n";
            echo "<select class=\"form-control\" id=\"role\" name=\"role\" size=\"1\">\n";
            $roles = array("Administrator","Student","Faculty");
            for ($i=0; $i<count($roles); $i++)
            {
               $selected = ($roles[$i] == $role_value) ? "selected" : "";
               echo "<option value=\"$roles[$i]\" $selected>$roles[$i]</option>\n";
            }
            echo "</select>\n";
            echo "<label for=\"avatar\">Profile Photo</label>\n";
            echo "<div class=\"input-group\" id=\"avatar\"><input class=\"form-control\" type=\"text\" readonly=\"\" value=\"$avatar_value\"><label class=\"input-group-btn\"><input type=\"file\" name=\"avatar\" style=\"display:none;\"><span class=\"btn\">Browse...</span></label></div>\n";
            if (!empty($avatar_value))
            {
               echo "<div class=\"thumbnail\"><div class=\"frame\"><img alt=\"$avatar_value\" src=\"$avatar_folder/$avatar_value\"></div></div>\n";
            }
            echo "<label for=\"extra1\">Phone Number</label>\n";
            echo "<input class=\"form-control\" id=\"extra1\" name=\"extra1\" size=\"50\" type=\"text\" value=\"" . $extra1_value . "\">\n";
            echo "<label for=\"extra2\">Roll Number</label>\n";
            echo "<input class=\"form-control\" id=\"extra2\" name=\"extra2\" size=\"50\" type=\"text\" value=\"" . $extra2_value . "\">\n";
            echo "<label for=\"extra3\">Company</label>\n";
            echo "<input class=\"form-control\" id=\"extra3\" name=\"extra3\" size=\"50\" type=\"text\" value=\"" . $extra3_value . "\">\n";
            echo "<label for=\"active\">Status</label>\n";
            echo "<select class=\"form-control\" name=\"active\" size=\"1\"><option " . ($active_value == "0" ? "selected " : "") . "value=\"0\">inactive</option><option " . ($active_value != "0" ? "selected " : "") . "value=\"1\">active</option></select>\n";
            echo "<input class=\"btn\" type=\"submit\" name=\"cmdSubmit\" value=\"Save\">";
            echo "&nbsp;&nbsp;";
            echo "<input class=\"btn\" name=\"cmdBack\" type=\"button\" value=\"Cancel\" onclick=\"location.href='" . basename(__FILE__) . "'\">\n";
            echo "</form>\n";
            echo "</div>\n";
         }
      }
      else
      {
         echo "<div id=\"header\"><a class=\"btn\" href=\"" . basename(__FILE__) . "?action=new\">New User</a>&nbsp;&nbsp;<a class=\"btn\" href=\"" . basename(__FILE__) . "?action=logout\">Logout</a>\n";
         echo "<div id=\"filter\">\n";
         echo "<label>Search: </label> <input class=\"form-control\" placeholder=\"\" type=\"search\">\n";
         echo "</div>\n</div>\n";
         echo "<table class=\"table table-striped table-hover\">\n";
         echo "<thead><tr><th>Username</th><th>Fullname</th><th>Email</th><th>Status</th><th>Action</th></tr></thead>\n";
         echo "<tbody>\n";
         foreach($items as $line)
         {
            list($username, $password, $email, $fullname, $active) = explode('|', trim($line));
            echo "<tr>\n";
            echo "<td>" . $username . "</td>\n";
            echo "<td>" . $fullname . "</td>\n";
            echo "<td>" . $email . "</td>\n";
            echo "<td>" . ($active == "0" ? "inactive" : "active") . "</td>\n";
            echo "<td>\n";
            echo "   <a href=\"" . basename(__FILE__) . "?action=edit&id=" . $username . "\" title=\"Edit\"><i class=\"icon-edit\"></i></a>&nbsp;\n";
            echo "   <a href=\"" . basename(__FILE__) . "?action=delete&id=" . $username . "\" title=\"Delete\"><i class=\"icon-delete\"></i></a>\n";
            echo "</td>\n";
            echo "</tr>\n";
         }
         echo "</tbody>\n";
         echo "</table>\n";
         echo "<div id=\"footer\">\n";
         echo "<ul id=\"pagination\">\n";
         echo "<li class=\"disabled\"><a href=\"#\">&laquo; Prev</a></li>\n";
         echo "<li class=\"disabled\"><a href=\"#\">Next &raquo;</a></li>\n";
         echo "</ul>\n";
         echo "</div>\n";
      }
   }
?>
</body>
</html>
