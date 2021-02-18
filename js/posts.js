document.addEventListener("DOMContentLoader", init);

function init(evt) {
    let posts = document.querySelectorAll('.post-box');
    const height = post.height;
    const width = post.width;
    for (let post of posts)
        post.addEventListener('mousemove', handleMove);

}



function handleMove(e) {
    const xVal = e.layerX
    const yVal = e.layerY

    const yRotation = 20 * ((xVal - width / 2) / width)
    const xRotation = -20 * ((yVal - height / 2) / height)
    const string = 'perspective(500px) scale(1.1) rotateX(' + xRotation + 'deg) rotateY(' + yRotation + 'deg)'

    this.style.transform = string
}
