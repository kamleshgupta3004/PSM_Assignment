// this line work after page ready
$(document).ready(function() {
    //get on page load list function
    getRecordList(1);
    //after click fetch record from url
    $('#previewButton').click(function() {
        $("#message").hide();
        var url = $('#urlInput').val().trim();
        //validate url here by using function
        if (!isValidUrl(url)) {
           $("#message").html('Invalid URL');
           $("#message").show();
           return false;
        }
        if (url !== '') {
            $("#previewButton").attr("disabled", true);
            $.ajax({
                url: baseurl+'previewurl',
                type: 'POST',
                data: { url: url },
                success: function(response) {
                    let record = JSON.parse(response);
                    if(record.status==1){
                        //genrate daynamic table preview 
                        var tableBody = $('#dataTable tbody');
                        console.log(record.data);
                        let row = '<tr>' +
                        '<td>' + record.data.title + '</td>' +
                        '<td>' + record.data.description + '</td>' +
                        '<td><img src="' + record.data.image + '" style="max-width: 100px; max-height: 100px;"></td>' +
                      '</tr>';
                      //set record on table
                      tableBody.html(row);
                      $(".preview-section").show();

                      //store in hidden record to save for db if click save button by user
                      $("#record").val(JSON.stringify(record.data));
                      //set url in hidden field may be user modify url after fetch preview
                      $("#url").val(url);
                    }
                    else{
                        //display message for user 
                        $("#message").html(record.message);
                        $("#message").show();
                    }
                    $("#previewButton").attr("disabled", false);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        }
    });
    // this event to submit record after click here i am using await ajax for after receiving response call list record function 
    $('#submitRecord').click(async function() {
        $("#message").hide();
        var record = $('#record').val();
        var url = $('#url').val();
        if (record !== '') {
            try {
                let response = await $.ajax({
                    url: baseurl + 'submitrecord',
                    type: 'POST',
                    data: { data: record, url: url }
                });
                let res = JSON.parse(response);
                // Call getRecordList function after receiving response
                getRecordList(1);
                $(".preview-section").hide();
                alert(res.msg);
            } catch (error) {
                console.error('AJAX Error:', error);
            }
        }
    });

    // Event listener for pagination links
    $(document).on('click', '.pagelink', function(e) {
        e.preventDefault();
        var page = $(this).attr('data-ci-pagination-page');
        getRecordList(page);
    });

});
// Function to validate URL
function isValidUrl(url) {
    // Regular expression for URL validation
    var urlPattern = new RegExp('^https?:\\/\\/' + // Protocol (http or https)
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // Domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR IPv4
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // Port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // Query string
        '(\\#[-a-z\\d_]*)?$', 'i'); // Fragment locator

    return urlPattern.test(url);
}

function getRecordList(page){
    $.ajax({
        url: baseurl+"recordlist/" + page,
        type: "GET",
        dataType: "json",
        success: function(data) {
            // Populate previews
            var tableBody1 = $('#dataTable1 tbody');
            var rec ='';
            if(data.previews==0){
                tableBody1.html('<tr><td colspan="4" style="text-align: center;">No record Found</td></tr>');
            }
            else{
                $.each(data.previews, function(index, preview) {
                    rec += '<tr>' +
                    '<td>' + preview.url + '</td>' +
                    '<td>' + preview.title + '</td>' +
                    '<td>' + preview.description + '</td>' +
                    '<td><img src="' + preview.image + '" style="max-width: 100px; max-height: 100px;"></td>' +
                '</tr>';
                });
                //apned record in this section
                tableBody1.html(rec);
                // Populate pagination links
                $('.pagination').html(data.links);
            }
            $(".saved-records").show();
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
        }
    });
}
