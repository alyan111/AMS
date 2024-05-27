<!-- <footer class="main-footer">
    <div class="footer-left">
      
    </div>
    <?php
    if ($this->ion_auth->logged_in()) {
      $current_url = current_url(); // Get the current page's URL
      $chat_page_url = base_url('chat'); // URL of the chat page
    
      // Check if the current URL is not the chat page
      if ($current_url !== $chat_page_url) {
        ?>
  <a href="<?= $chat_page_url ?>" class="chat-button">
    <i class="fas fa-comment text-primary"></i>
    <?= get_chat_message_count() ?>
  </a>
<?php
      }
    }
    ?>
</footer> -->
<div class="footer">
  <div class="copyright">
    <p><?= htmlspecialchars(footer_text()) ?></p>
  </div>
</div>


<style>
  .notification-box {
    display: none;
    position: fixed;
    top: 0%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #fff;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    padding: "40px 10px";
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;

    color: #222;

  }

  .notification-box p {
    margin: 0 0 10px;

  }

  .notification-box div {
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .notification-box button {
    padding: 10px 20px;
    margin-right: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  #allowButton {
    background-color: #4caf50;
    color: #fff;
  }

  #denyButton {
    background-color: #f44336;
    color: #fff;
  }
</style>

<div class="notification-box" id="notificationBox">
  <p>Would you like to receive notifications on this device?</p>
  <div>
    <button id="allowButton">Allow</button>
    <button id="denyButton">Deny</button>
  </div>
</div>

<script type="text/javascript" src="<?php echo base_url("/assets/js/notification_box.js") ?>"></script>