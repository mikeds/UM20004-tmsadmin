$(document).ready(function(){
    let fpath = base_url + 'income-groups/merchant-list/' + params;

    new DataTree({
        fpath: fpath,
        container: '#tree-view',
        json: true,
        startExpanded: true
    });

    console.log(params);
});
