
var scriptSrc = document.currentScript.src;
var packagePath = scriptSrc.replace('/scripts/package1.js', '').trim();
var re = /([a-f0-9]{8}(?:-[a-f0-9]{4}){3}-[a-f0-9]{12})/i;
var packageId = re.exec(scriptSrc.toLowerCase())[1];

var settings = {
  "method": "POST",
  "url": packagePath + "/testApis.php"
};

$(document).ready(function () {

  testApis()

})


function testApis() {
  $("#api-form").on("submit", function (event) {
    event.preventDefault();
    var formValues = getFormValues($(this))
    var data = {
      "packageId": formValues.packageId,
      "merchantId": formValues.merchantId
    };
    $("#json-response-display").html('<span class="simplePlugin-number">Loading....</span>');
    settings.data = JSON.stringify(data)
    settings.success = function (response) {
      console.log(response)
      $("#json-response-display").html(syntaxHighlight(JSON.stringify(JSON.parse(response), function (key, value) {
        if (Array.isArray(value) && value.length == 0) {
          return "[]";
        }
        return value;
      }, 4)))
    }
    $.ajax(settings)
  })
}

function getFormValues(formValues) {
  var result = formValues.serializeArray().reduce(function (obj, item) {
    obj[item.name] = item.value;
    return obj;
  }, {})
  return result
}

function isObject(value) {
  return value && typeof value === 'object' && value.constructor === Object;
}

//json color coding
function syntaxHighlight(json) {
  json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
    var cls = 'simplePlugin-number';
    if (/^"/.test(match)) {
      if (/:$/.test(match)) {
        cls = 'simplePlugin-key';
      } else {
        cls = 'simplePlugin-string';
        if (match == `"Failed"`) {
          cls = 'simplePlugin-null';
        } else if (match == `"Passed"`) {
          cls = 'simplePlugin-number';
        }
      }
    } else if (/true|false/.test(match)) {
      cls = 'simplePlugin-boolean';
    } else if (/null/.test(match)) {
      cls = 'simplePlugin-null';
    }
    return '<span class="' + cls + '">' + match + '</span>';
  });
}
