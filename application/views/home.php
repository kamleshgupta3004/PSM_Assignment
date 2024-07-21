<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PSM CI Assignment URL Preview and Save</title>
    <link rel="stylesheet" href="<?php echo base_url()?>/assets/css/style.css">
  </head>
  <body>
    <div class="container">
      <div class="input-section">
        <h2>Enter URL</h2>
        <div class="input-group">
          <input type="text" id="urlInput" placeholder="Enter URL...">
          <button id="previewButton">Preview</button>
        </div>
        <span id="message" style="color:red"></span>
      </div>
      <div class="preview-section" style="display:none">
        <h2>Preview</h2>
        <div id="previewContainer" class="previewButton" class="preview-container">
          <input type="hidden" id="record" name="record" value="">
          <input type="hidden" id="url" name="url" value="">
          <button id="submitRecord">Save Record</button>
          <table id="dataTable">
            <thead>
              <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
              </tr>
            </thead>
            <tbody>
              <!-- Data will be populated dynamically using jQuery -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="saved-records">
        <h3>Record List</h3>
        <table id="dataTable1">
          <thead>
            <tr>
              <th>Url</th>
              <th>Title</th>
              <th>Description</th>
              <th>Image</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data will be populated dynamically using jQuery -->
          </tbody>
        </table>
        <div class="pagination">
          <!-- Pagination links will be dynamically loaded here -->
        </div>
      </div>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <!-- load comman code--> <?php $this->load->view('common_footer'); ?>
      <!--this page js -->
      <script src="<?php echo base_url()?>assets/js/home.js"></script>
    </body>
</html>