document.addEventListener('DOMContentLoaded', () => {
    // Tabs
    document.querySelectorAll('.bpsm-tabs-container').forEach(container => {
        const tabs = container.querySelectorAll('.bpsm-tab');
        const contents = container.querySelectorAll('.bpsm-tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('bpsm-tab-active'));
                tab.classList.add('bpsm-tab-active');

                contents.forEach(c => c.style.display = 'none');
                const target = tab.getAttribute('data-target');
                const targetContent = container.querySelector(`#${target}`);
                if(targetContent) targetContent.style.display = 'block';
            });
        });
    });

    // Collapsible Cards
    document.querySelectorAll('.bpsm-collapse-header').forEach(header => {
        header.addEventListener('click', () => {
            const body = header.nextElementSibling;
            body.style.display = body.style.display === 'block' ? 'none' : 'block';
        });
    });
});
