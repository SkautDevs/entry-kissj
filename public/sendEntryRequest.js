function sendEntryRequest(form)
{
    form.addEventListener('submit', async event => {
        event.preventDefault();
        const resultElement = form.getElementsByClassName("formResult")[0];
        resultElement.innerText = "↻";

        try {
            const data = new FormData(form);
            const response = await fetch(
                form.action,
                {
                    method: 'POST',
                    body: data,
                    headers: {
                        'authorization': 'Bearer' + document.cookie.match(new RegExp('(^| )bearerToken=([^;]+)'))[2],
                    }
                },
            );

            const responseData = await response.json();
            if (response.ok) {
                resultElement.innerText = "✔"
            }
        } catch (error) {
            resultElement.innerText = "☠";
            console.log(error.message);
        }
    });
}