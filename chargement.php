
<?php
<!DOCTYPE html>
<!-- Coding by CodingLab | www.codinglabweb.com-->
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Estuaire Achats </title>

        <!-- CSS -->
        <link rel="stylesheet" href="css/style.css">

    </head>
    <body>
        <div class="container">
            <div class="circular-progress">
                <span class="progress-value">0%</span>
            </div>

            <span class="text">Patientez</span>
        </div>


        <style>
            /* Google Fonts - Poppins */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}
body{
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgb(175, 8, 8);
}
.container{
    display: flex;
    width: 420px;
    padding: 50px 0;
    border-radius: 8px;
    background: #fff;
    row-gap: 30px;
    flex-direction: column;
    align-items: center;
}
.circular-progress{
    position: relative;
    height: 250px;
    width: 250px;
    border-radius: 50%;
    background: conic-gradient(#af1111 3.6deg, #ededed 0deg);
    display: flex;
    align-items: center;
    justify-content: center;
}
.circular-progress::before{
    content: "";
    position: absolute;
    height: 210px;
    width: 210px;
    border-radius: 50%;
    background-color: #fff;
}
.progress-value{
    position: relative;
    font-size: 40px;
    font-weight: 600;
    color: #c70b0b;
}
.text{
    font-size: 30px;
    font-weight: 500;
    color: #606060;
}
        </style>
        <!-- JavaScript -->
        <script >
            let circularProgress = document.querySelector(".circular-progress"),
    progressValue = document.querySelector(".progress-value");

let progressStartValue = 0,
    progressEndValue = 100,
    speed = 17;

    let progress = setInterval(() => {
        progressStartValue++;

        progressValue.textContent = `${progressStartValue}%`;
        circularProgress.style.background = `conic-gradient(#c70b0b ${progressStartValue * 3.6}deg, #ededed 0deg)`;

        if (progressStartValue == progressEndValue) {
            clearInterval(progress);
            // Ouvrir le lien presque immédiatement après la fin de la progression
            setTimeout(() => {
                window.location.href = 'http://localhost';
            }, 200); // Délai en millisecondes (ici 200ms)
        }
    }, speed);

        </script>
    </body>
</html>

?>
