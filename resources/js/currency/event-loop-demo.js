export function runEventLoopDemo() {
    const timeline = [];
    const recordStep = (label) => timeline.push(label);

    recordStep('call-stack: synchronous setup');

    setTimeout(() => {
        recordStep('callback-queue: setTimeout callback');
        window.currencyEventLoopTimeline = timeline;
    }, 0);

    Promise.resolve('microtask-queue: promise resolved')
        .then((label) => {
            recordStep(label);
            return 'microtask-queue: chained then';
        })
        .then(recordStep)
        .catch((error) => {
            recordStep(`promise rejected: ${error.message}`);
        });

    return timeline;
}
