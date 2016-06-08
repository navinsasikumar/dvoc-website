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

    $("#officer-form").delegate("input[type=text]", "keyup", function() {
        var officer = $(this).attr('name');

        var searchTerm = $("#dvoc-" + officer + "-name").val();
        if (searchTerm.length < 2) {
            return '';
        }
        var data = {
            'action': 'dvoc_search_member',
            's': searchTerm
        };
        $.post(ajax_object.ajax_url, data, function(response) {
            var ret = "";
            response = JSON.parse(response);
            for (var i = 0; i < response.length; i++) {
                var member = response[i];
                var fullName = member.first_name + ' ' + member.last_name;
                ret += "<a class='dvoc-officer-select' href='javascript:dvoc_officer_select(\"" + officer + "\", " + member.id + ", \"" + fullName + "\");'>" + fullName + "</a>";
            };
            $("#dvoc-list-" + officer + "-names").html(ret);
        });
    });

    $("#award-form").delegate("input[type=text]", "keyup", function() {
        var award = $(this).attr('name');

        var searchTerm = $("#dvoc-" + award + "-name").val();
        if (searchTerm.length < 2) {
            return '';
        }
        var data = {
            'action': 'dvoc_search_member',
            's': searchTerm
        };
        $.post(ajax_object.ajax_url, data, function(response) {
            var ret = "";
            response = JSON.parse(response);
            for (var i = 0; i < response.length; i++) {
                var member = response[i];
                var fullName = member.first_name + ' ' + member.last_name;
                ret += "<a class='dvoc-award-select' href='javascript:dvoc_award_select(\"" + award + "\", " + member.id + ", \"" + fullName + "\");'>" + fullName + "</a>";
            };
            $("#dvoc-list-" + award + "-names").html(ret);
        });
    });

});

function dvoc_member_select(id, name) {
    jQuery("#dvoc-member-name").val(name);
    jQuery("#dvoc-list-member-names").html('');
    jQuery("#dvoc-member-id").val(id);
}

function dvoc_officer_select(officer, id, name) {
    jQuery("#dvoc-" + officer + "-name").val(name);
    jQuery("#dvoc-list-" + officer + "-names").html('');
    jQuery("#dvoc-" + officer + "-id").val(id);
}

function dvoc_award_select(award, id, name) {
    jQuery("#dvoc-" + award + "-name").val(name);
    jQuery("#dvoc-list-" + award + "-names").html('');
    jQuery("#dvoc-" + award + "-id").val(id);
}
