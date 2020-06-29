
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

    //get array of values from form and convert to object

    const formValues = getFormValues($(this))
    var data = {
      "apiType": formValues.api
    };

    settings.data = JSON.stringify(data)
    settings.success = function (response) {
      console.log(JSON.parse(response))
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
