<style>
    .btn-block,
    .btn-warning {
        text-transform: uppercase
    }
    .form-control {
        border-radius: 0;
        box-shadow: none;
        font: 300 17px/24px Poppins, sans-serif;
        padding: 12px 18px;
        height: 50px;
        border-color: #36305e;
        background: #f8fbfd;
        color: #36305e
    }
    .form-control::-moz-placeholder {
        opacity: 1;
        font: 300 17px/24px Poppins, sans-serif;
        color: #36305e
    }
    .form-control:-ms-input-placeholder {
        font: 300 17px/24px Poppins, sans-serif;
        color: #36305e
    }
    .form-control::-webkit-input-placeholder {
        font: 300 17px/24px Poppins, sans-serif;
        color: #36305e
    }
    .form-control:focus {
        background: 0 0;
        box-shadow: none
    }
    .form-control.alt {
        background: #FFF;
        border-color: #CCC
    }
    textarea.form-control {
        resize: none
    }
    .form-group {
        margin-bottom: 20px
    }
    .btn:focus,
    .btn:hover {
        background: #374048;
        border-color: #374048;
        color: #fff
    }
    .btn-primary {
        border: none;
        background: #0084ff;
        color: #FFF;
        padding: 0 28px
    }
    .btn-primary.btn-lg {
        padding: 0 85px;
        border-radius: 60px;
        font: 500 27px/72px Poppins, sans-serif;
        margin-bottom: 77px
    }
    @media (max-width: 991px) {
        .btn-primary.btn-lg {
            padding: 0 70px;
            font-size: 18px;
            line-height: 60px;
            margin-bottom: 15px
        }
    }
    @media (max-width: 767px) {
        .btn-primary.btn-lg {
            padding: 0 65px
        }
    }
    .btn-kickstarter {
        border: none;
        background: #14e06e;
        color: #FFF;
        padding: 0 28px
    }
    .btn-kickstarter.btn-lg {
        padding: 0 45px;
        border-radius: 60px;
        font: 500 22px/65px Poppins, sans-serif;
        margin-bottom: 77px
    }
    @media (max-width: 991px) {
        .btn-kickstarter.btn-lg {
            padding: 0 70px;
            font-size: 18px;
            line-height: 60px;
            margin-bottom: 15px
        }
    }
    @media (max-width: 767px) {
        .btn-kickstarter.btn-lg {
            padding: 2px 40px 16px;
            line-height: 40px
        }
        .btn-kickstarter.btn-lg img {
            display: block
        }
    }
    .btn-kickstarter:focus,
    .btn-kickstarter:hover {
        background-color: #10b157
    }
    .btn-indegogo {
        border: none;
        background: #eb1478;
        color: #FFF;
        padding: 0 28px
    }
    .btn-indegogo.btn-lg {
        padding: 0 45px;
        border-radius: 60px;
        font: 500 22px/65px Poppins, sans-serif;
        margin-bottom: 77px
    }
    @media (max-width: 991px) {
        .btn-indegogo.btn-lg {
            padding: 0 70px;
            font-size: 18px;
            line-height: 60px;
            margin-bottom: 15px
        }
    }
    @media (max-width: 767px) {
        .btn-indegogo.btn-lg {
            padding: 2px 40px 16px;
            line-height: 40px
        }
        .btn-indegogo.btn-lg img {
            display: block
        }
    }
    .btn-indegogo:focus,
    .btn-indegogo:hover {
        background-color: #bc1060
    }
    .btn-warning {
        font: 500 16px/40px Poppins, sans-serif;
        padding: 0 35px;
        border-radius: 30px;
        color: #fff;
        margin-left: 10px;
        border: none
    }
    .btn-warning:focus,
    .btn-warning:hover {
        background: #374048;
        color: #fff
    }
    .btn-warning.btn-lg {
        padding: 0 85px;
        border-radius: 60px;
        font: 500 27px/72px Poppins, sans-serif;
        margin-bottom: 77px
    }
    @media (max-width: 991px) {
        .btn-warning.btn-lg {
            padding: 0 70px;
            font-size: 18px;
            line-height: 60px;
            margin-bottom: 15px
        }
        .btn-warning {
            margin-bottom: 35px
        }
    }
    .btn-link {
        font: 300 15px/14px Poppins, sans-serif;
        border-bottom: 1px solid;
        text-decoration: none;
        color: #007eff;
        border-color: #a3c5ff
    }
    .btn-block {
        font: 600 19px/63px Poppins, sans-serif;
        border: none;
        transition: all .3s linear 0s;
        border-radius: 0;
        background: #36305e;
        color: #FFF;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis
    }
    .btn-block.btn-rounded {
        border-radius: 4px;
        font: 600 26px/80px Poppins, sans-serif
    }
    @media (max-width: 767px) {
        .btn-warning.btn-lg {
            padding: 0 65px
        }
        .btn-block.btn-rounded {
            font: 600 18px/60px Poppins, sans-serif
        }
    }
</style>
   
<script>

    $('#satinal').submit(function(e) {
        e.preventDefault(); // Prevent Default Submission
        $.ajax({
            url: '/iletisim/',
            type: 'POST',
            data: $(this).serialize(), // it will serialize the form data
            dataType: 'json'
        }).done(function(data) {
            if (data.status == "true") {
                window.location.assign(data.token);
            } else {
                swal(
                    'Oops...',
                    data.message,
                    'error'
                )
            }
        });
    })
</script>

<div class="pace  pace-inactive">
	<div class="pace-progress" style="transform: translate3d(100%, 0px, 0px);" data-progress-text="100%" data-progress="99">
		<div class="pace-progress-inner"></div>
	</div>
    <div class="row" style="width: 800px;">
      <div class="col-xs-12">
         <div class="center-content">
            <div class="row">
               <div class="col-xs-8">
                  <form method="post" id="satinal" class="product-info">
                     <div class="row" style="padding: 10px 10px">
                        <div class="form-group col-sm-12">
                           <h2 class="edition"><?php echo $listele["site_baslik"]; ?> İletişim</h2>
                           <p>Satın aldığınız bir üründe sıkıntı yaşarsanız, aşağıdaki iletişim bilgilerimizden bize mesaj yoluyla ulaşabilirsiniz.</p>
                        </div>
						<div class="form-group col-sm-12">
						   <input name="gmail" class="form-control" placeholder="Musteri Hizmetleri : musteri@technopazarim.com" disabled required="required" type="text">
						</div>
                        <div class="form-group col-sm-12">
                           <input name="hotmail" class="form-control" placeholder="Kurumsal Hizmetler : kurumsal@technopazarim.com" disabled required="required" autocomplete="off" type="text">
                        </div>
                        <div class="form-group col-sm-12">
                           <input name="hotmail" class="form-control" placeholder="Canlı Destek : Altdan Ulasabilirsin!" disabled required="required" autocomplete="off" type="text">
                        </div>
						<div class="form-group col-sm-12 mobil">
                           <a href="tel:+-"><input name="telefon" class="form-control bfh-phone" value="Telefon : +-" required="required" data-format="dddd ddd dd dd" required="required" autocomplete="off" type="submit"></a>
                        </div>
                        <div class="form-group col-sm-12">
                           <a href="https://www.facebook.com/OfficialTechnoLite"><input name="facebook" class="form-control" value="Facebook : https://www.facebook.com/OfficialTechnoLite" required="required" autocomplete="off" type="submit"></a>
                        </div>
                        <div class="form-group col-sm-12">
                           <a href="https://www.instagram.com/yasint27/"><input name="Instagram" class="form-control bfh-phone" value="Instagram : https://www.instagram.com/yasint27/" data-format="dddd ddd dd dd" required="required" autocomplete="off" type="submit"></a>
                        </div>
                        <div class="form-group col-sm-12">
                           <label><input name="sartlar" value="kabul" required="required" type="checkbox" hidden></label>
                        </div>
                        <input name="technopazarim_token" value="7ae46b461f889290404f9d86befc94a1" type="hidden">
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- Services Section Start -->


<!-- Scripts -->
<!-- jQuery -->
<script src="iletisim_dosyalar/jquery-1.js"></script>
<script src="iletisim_dosyalar/jquery_005.js"></script>
<!-- Pace -->
<script src="iletisim_dosyalar/pace.js"></script>
<!-- Bootsrap core -->
<script src="iletisim_dosyalar/bootstrap.js"></script>

<!-- Plugins -->
<script src="iletisim_dosyalar/title-animated.js"></script>
<script src="iletisim_dosyalar/jquery_002.js"></script>
<script src="iletisim_dosyalar/jquery.js"></script>
<script src="iletisim_dosyalar/device-detect.js"></script>
<script src="iletisim_dosyalar/owl.js"></script>
<script src="iletisim_dosyalar/jquery_004.js"></script>
<script src="iletisim_dosyalar/jquery_003.js"></script>
<script src="iletisim_dosyalar/live_chat_facbook.js"></script>
<script type="text/javascript" src="iletisim_dosyalar/moment-with-locales.js"></script>
<script>moment.locale("tr");</script>
<script type="text/javascript" src="iletisim_dosyalar/livestamp.js"></script>

<!-- Custom Script -->
<script src="iletisim_dosyalar/function.js"></script>
<script type="text/javascript">
   jQuery(document).ready(function($) {
        $(window).scroll(function() {
            var e = $(window).width();
            //680 >= e ? f_create_cki('f_chat_open', '0', 1) : f_create_cki('f_chat_open', '1', 1)
        })
    }), setTimeout(function() {
          var visit = f_read_cki("check_fist_vist_f");
          if ( visit == "1" ) {
              f_ck_chat();
          }else{
              f_ck_chat();
              fb_ehide("f-chat-conent");
              f_create_cki('f_chat_open', '0', 1);
              f_bt_start_chat();
         }
    }, 1000);

</script>