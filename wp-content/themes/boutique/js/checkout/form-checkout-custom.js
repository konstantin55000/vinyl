let form = document.querySelector('name="checkout"');
console.log('form', form);

//if (typeof(form) != 'undefined' && form !== null )
//{
    
form.setAttribute('action', 'https://shop.westernbid.info');

console.log('form', form);
form.addEventListener('submit',  function(e){
    e.preventDefault();
    e.stopPropagation();
    alert('checkout submit');
    document.getElementById('first_name').value = document.getElementById('billing_first_name').value;
    document.getElementById('last_name').value = document.getElementById('billing_last_name').value;
    
    
    document.getElementById('address1').value = document.getElementById('billing_address_1').value;
    document.getElementById('address2').value = document.getElementById('billing_address_2').value;
    
    document.getElementById('city').value = document.getElementById('billing_city').value;
    document.getElementById('country').value = document.getElementById('billing_country').value;
    document.getElementById('state').value = document.getElementById('billing_state').value;
    
    
    document.getElementById('zip').value = document.getElementById('billing_postcode').value;    
    document.getElementById('phone').value = document.getElementById('billing_phone').value;     
    document.getElementById('email').value = document.getElementById('billing_email').value; 
    
    form.submit();
    //e.stopImmediatePropagation();
    console.log('form.' , form);
    return false;
} );
    
//}