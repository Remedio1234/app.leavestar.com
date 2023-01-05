<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountingTokenRequest;
use App\Http\Requests\UpdateAccountingTokenRequest;
use App\Repositories\AccountingTokenRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AccountingTokenController extends AppBaseController
{
    /** @var  AccountingTokenRepository */
    private $accountingTokenRepository;

    public function __construct(AccountingTokenRepository $accountingTokenRepo)
    {
        $this->accountingTokenRepository = $accountingTokenRepo;
    }

    /**
     * Display a listing of the AccountingToken.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accountingTokenRepository->pushCriteria(new RequestCriteria($request));
        $accountingTokens = $this->accountingTokenRepository->all();

        return view('accounting_tokens.index')
            ->with('accountingTokens', $accountingTokens);
    }

    /**
     * Show the form for creating a new AccountingToken.
     *
     * @return Response
     */
    public function create()
    {
        return view('accounting_tokens.create');
    }

    /**
     * Store a newly created AccountingToken in storage.
     *
     * @param CreateAccountingTokenRequest $request
     *
     * @return Response
     */
    public function store(CreateAccountingTokenRequest $request)
    {
        $input = $request->all();

        $accountingToken = $this->accountingTokenRepository->create($input);

        Flash::success('Accounting Token saved successfully.');

        return redirect(route('accountingTokens.index'));
    }

    /**
     * Display the specified AccountingToken.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $accountingToken = $this->accountingTokenRepository->findWithoutFail($id);

        if (empty($accountingToken)) {
            Flash::error('Accounting Token not found');

            return redirect(route('accountingTokens.index'));
        }

        return view('accounting_tokens.show')->with('accountingToken', $accountingToken);
    }

    /**
     * Show the form for editing the specified AccountingToken.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $accountingToken = $this->accountingTokenRepository->findWithoutFail($id);

        if (empty($accountingToken)) {
            Flash::error('Accounting Token not found');

            return redirect(route('accountingTokens.index'));
        }

        return view('accounting_tokens.edit')->with('accountingToken', $accountingToken);
    }

    /**
     * Update the specified AccountingToken in storage.
     *
     * @param  int              $id
     * @param UpdateAccountingTokenRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccountingTokenRequest $request)
    {
        $accountingToken = $this->accountingTokenRepository->findWithoutFail($id);

        if (empty($accountingToken)) {
            Flash::error('Accounting Token not found');

            return redirect(route('accountingTokens.index'));
        }

        $accountingToken = $this->accountingTokenRepository->update($request->all(), $id);

        Flash::success('Accounting Token updated successfully.');

        return redirect(route('accountingTokens.index'));
    }

    /**
     * Remove the specified AccountingToken from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $accountingToken = $this->accountingTokenRepository->findWithoutFail($id);

        if (empty($accountingToken)) {
            Flash::error('Accounting Token not found');

            return redirect(route('accountingTokens.index'));
        }

        $this->accountingTokenRepository->delete($id);

        Flash::success('Accounting Token deleted successfully.');

        return redirect(route('accountingTokens.index'));
    }
}
