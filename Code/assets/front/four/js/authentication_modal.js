$(document).ready(function () {
  $('#exampleModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var modal = $(this);

    // Make separate AJAX requests for login and signup content
    var loginRequest = $.ajax({
      url: "<?= base_url('auth') ?>",
      type: "GET"
    });

    var signupRequest = $.ajax({
      url: "<?= base_url('auth/register') ?>",
      type: "GET"
    });

    // Handle successful responses using Promise.all
    $.when(loginRequest, signupRequest).done(function (loginResponse, signupResponse) {
      modal.find('.login .modal-body-login').html(loginResponse[0]);
      modal.find('.signup .modal-body-signup').html(signupResponse[0]);
      console.log("Login and signup forms loaded successfully!");
    }).fail(function (jqXHR, textStatus, errorThrown) {
      console.error("Error loading forms:", textStatus, errorThrown);
      // Handle errors (e.g., display error message)
    });
  });

  const loginRadio = $('#login');
  const signupRadio = $('#signup');
  const loginContent = $('.modal-body-login');
  const signupContent = $('.modal-body-signup');
  const sliderTab = $('.slider-tab');

  function showLogin() {
    loginContent.show();
    signupContent.hide();
    sliderTab.css('left', '0');
    loginContent.parent().css('height', "0px");

  }

  function showSignup() {
    loginContent.hide();
    signupContent.show();
    loginContent.parent().css('height', "0px");
    sliderTab.css('left', '50%');
  }

  loginRadio.change(showLogin);
  signupRadio.change(showSignup);

  $('.tp-btn').click(function () {
    signupRadio.click();
  });

  // Initial display setup
  showLogin();
});




////// modal based css ends here
////// modal based css ends here
////// modal based css ends here
////// modal based css ends here
