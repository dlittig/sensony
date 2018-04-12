$(document).ready(function() {
    const element = $('div.request_json > * > div.form-control')
    if($(element).length === 0) return
    if($(element).text().trim() === 'Null') return

    const text = $(element).text()
    const obj = JSON.parse(text)

    $(element).text(JSON.stringify(obj, null, 2))
})