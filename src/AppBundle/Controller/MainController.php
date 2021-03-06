<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MainController extends Controller
{
    public function initialize(Request $request)
    {
        parent::initialize($request);
        $lines = $this->getLines();
        $this->toView('isHomepage', false, false);
        $this->toView('lines', $lines);
        $this->toView('lineData', json_encode($lines), false);
    }

    public function indexAction()
    {
        $this->toView('isHomepage', true, false);
        return $this->renderTemplate('main:index');
    }

    public function settingsAction()
    {
        return $this->renderTemplate('main:settings');
    }

    public function lineAction(Request $request)
    {
        $lineKey = $request->get('line');
        $allLines = $this->fromView('lines');
        $line = null;
        foreach ($allLines as $l) {
            if ($l->getURLKey() == $lineKey) {
                $line = $l;
            }
        }
        if (!$line) {
            throw new HttpException(404, 'Line ' . $lineKey . ' does not exist.');
        }

        $this->setTitle($line->getName())
            ->toView('line', $line);

        return $this->renderTemplate('main:line');
    }

    public function styleguideAction()
    {
        return $this->renderTemplate('main:styleguide');
    }

    private function getLines()
    {
        $result = $this->get('app.services.line')->findAllWithStatus();
        if ($result->isEmpty()) {
            throw new HttpException(503, 'No Line Data');
        }
        return $result->getDomainModels();
    }
}
