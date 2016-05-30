jQuery(document).ready(function($) {
    $("#dvoc-member-name").keyup(function() {
        var searchTerm = $("#dvoc-member-name").val();
        if (searchTerm.length < 2) {
            return '';
        }
        var data = {
            'action': 'dvoc_search_member',
            's': $("#dvoc-member-name").val()
        };
        $.post(ajax_object.ajax_url, data, function(response) {
            var ret = "";
            response = JSON.parse(response);
            for (var i = 0; i < response.length; i++) {
                var member = response[i];
                var fullName = member.first_name + ' ' + member.last_name;
                ret += "<a class='dvoc-member-select' href='javascript:dvoc_member_select(" + member.id + ", \"" + fullName + "\");'>" + fullName + "</a>";
            };
            $("#dvoc-list-member-names").html(ret);
        });
    });

});

function dvoc_member_select(id, name) {
    jQuery("#dvoc-member-name").val(name);
    jQuery("#dvoc-list-member-names").html('');
    jQuery("#dvoc-committe-member").val(id);
}
